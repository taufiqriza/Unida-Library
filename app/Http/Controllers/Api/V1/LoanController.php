<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LoanController extends BaseController
{
    public function active(Request $request)
    {
        $member = $request->user();
        $loans = $member->loans()
            ->with(['item.book.authors', 'item.location'])
            ->where('is_returned', false)
            ->orderBy('due_date')
            ->get();

        $loanLimit = $member->memberType?->loan_limit ?? 5;

        return $this->success([
            'loans' => $loans->map(fn($loan) => $this->formatLoan($loan)),
            'summary' => [
                'total_active' => $loans->count(),
                'overdue_count' => $loans->filter(fn($l) => $l->isOverdue())->count(),
                'loan_limit' => $loanLimit,
                'remaining_quota' => max(0, $loanLimit - $loans->count()),
            ],
        ]);
    }

    public function history(Request $request)
    {
        $loans = $request->user()->loans()
            ->with(['item.book.authors'])
            ->where('is_returned', true)
            ->orderByDesc('return_date')
            ->paginate($request->per_page ?? 20);

        return $this->paginated($loans->through(fn($loan) => [
            'id' => $loan->id,
            'book' => [
                'id' => $loan->item?->book?->id,
                'title' => $loan->item?->book?->title,
                'authors' => $loan->item?->book?->authors?->pluck('name') ?? [],
                'cover_url' => $loan->item?->book?->cover ? Storage::disk('public')->url($loan->item->book->cover) : null,
            ],
            'loan_date' => $loan->loan_date?->format('Y-m-d'),
            'due_date' => $loan->due_date?->format('Y-m-d'),
            'return_date' => $loan->return_date?->format('Y-m-d'),
            'was_overdue' => $loan->return_date && $loan->due_date && $loan->return_date->gt($loan->due_date),
        ]));
    }

    public function show(Request $request, $id)
    {
        $loan = $request->user()->loans()
            ->with(['item.book.authors', 'item.location', 'item.branch', 'fines'])
            ->find($id);

        if (!$loan) {
            return $this->error('Peminjaman tidak ditemukan', 404);
        }

        return $this->success($this->formatLoan($loan, true));
    }

    public function fines(Request $request)
    {
        $fines = $request->user()->fines()
            ->with(['loan.item.book'])
            ->orderByDesc('created_at')
            ->get();

        $totalAmount = $fines->sum('amount');
        $totalPaid = $fines->sum('paid_amount');

        return $this->success([
            'fines' => $fines->map(fn($fine) => [
                'id' => $fine->id,
                'loan' => [
                    'id' => $fine->loan?->id,
                    'book_title' => $fine->loan?->item?->book?->title,
                    'loan_date' => $fine->loan?->loan_date?->format('Y-m-d'),
                    'due_date' => $fine->loan?->due_date?->format('Y-m-d'),
                    'return_date' => $fine->loan?->return_date?->format('Y-m-d'),
                ],
                'days_overdue' => $fine->days_overdue ?? 0,
                'amount' => $fine->amount,
                'paid_amount' => $fine->paid_amount,
                'remaining' => $fine->amount - $fine->paid_amount,
                'is_paid' => $fine->is_paid,
                'created_at' => $fine->created_at?->toIso8601String(),
            ]),
            'summary' => [
                'total_fines' => $fines->count(),
                'total_amount' => $totalAmount,
                'total_paid' => $totalPaid,
                'total_unpaid' => $totalAmount - $totalPaid,
            ],
        ]);
    }

    public function finesSummary(Request $request)
    {
        $member = $request->user();
        $unpaidFines = $member->fines()->where('is_paid', false);

        return $this->success([
            'total_unpaid' => $unpaidFines->sum('amount') - $unpaidFines->sum('paid_amount'),
            'unpaid_count' => $unpaidFines->count(),
        ]);
    }

    protected function formatLoan($loan, bool $detailed = false): array
    {
        $data = [
            'id' => $loan->id,
            'book' => [
                'id' => $loan->item?->book?->id,
                'title' => $loan->item?->book?->title,
                'authors' => $loan->item?->book?->authors?->pluck('name') ?? [],
                'cover_url' => $loan->item?->book?->cover ? Storage::disk('public')->url($loan->item->book->cover) : null,
            ],
            'item' => [
                'barcode' => $loan->item?->barcode,
                'call_number' => $loan->item?->call_number,
            ],
            'loan_date' => $loan->loan_date?->format('Y-m-d'),
            'due_date' => $loan->due_date?->format('Y-m-d'),
            'days_remaining' => $loan->due_date ? max(0, now()->diffInDays($loan->due_date, false)) : 0,
            'is_overdue' => $loan->isOverdue(),
            'can_renew' => $loan->renew_count < ($loan->item?->book?->max_renew ?? 2),
            'renew_count' => $loan->renew_count,
        ];

        if ($detailed) {
            $data['item']['location'] = $loan->item?->location?->name;
            $data['item']['branch'] = $loan->item?->branch?->name;
            $data['fines'] = $loan->fines->map(fn($f) => [
                'amount' => $f->amount,
                'is_paid' => $f->is_paid,
            ]);
        }

        return $data;
    }
}
