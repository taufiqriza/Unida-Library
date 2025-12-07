<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberLoanController extends Controller
{
    public function active(Request $request)
    {
        $loans = $request->user()
            ->loans()
            ->with(['item.book', 'item.location'])
            ->where('is_returned', false)
            ->orderBy('due_date')
            ->get()
            ->map(fn($loan) => $this->formatLoan($loan));

        return response()->json(['data' => $loans]);
    }

    public function history(Request $request)
    {
        $loans = $request->user()
            ->loans()
            ->with(['item.book'])
            ->where('is_returned', true)
            ->latest('return_date')
            ->paginate(10);

        return response()->json([
            'data' => $loans->map(fn($loan) => $this->formatLoan($loan)),
            'meta' => [
                'current_page' => $loans->currentPage(),
                'last_page' => $loans->lastPage(),
                'total' => $loans->total(),
            ],
        ]);
    }

    public function fines(Request $request)
    {
        $fines = $request->user()
            ->fines()
            ->with('loan.item.book')
            ->latest()
            ->get()
            ->map(fn($fine) => [
                'id' => $fine->id,
                'book' => $fine->loan?->item?->book?->title ?? '-',
                'amount' => $fine->amount,
                'paid_amount' => $fine->paid_amount,
                'remaining' => $fine->remaining,
                'is_paid' => $fine->is_paid,
                'description' => $fine->description,
                'created_at' => $fine->created_at->format('d M Y'),
            ]);

        $total_unpaid = $fines->where('is_paid', false)->sum('remaining');

        return response()->json([
            'data' => $fines,
            'total_unpaid' => $total_unpaid,
        ]);
    }

    protected function formatLoan($loan)
    {
        $book = $loan->item?->book;
        $isOverdue = !$loan->is_returned && $loan->due_date < now();

        return [
            'id' => $loan->id,
            'book' => [
                'id' => $book?->id,
                'title' => $book?->title ?? '-',
                'cover' => $book?->cover ? asset('storage/' . $book->cover) : null,
                'authors' => $book?->authors?->pluck('name')->join(', ') ?? '-',
            ],
            'barcode' => $loan->item?->barcode,
            'location' => $loan->item?->location?->name,
            'loan_date' => $loan->loan_date->format('d M Y'),
            'due_date' => $loan->due_date->format('d M Y'),
            'return_date' => $loan->return_date?->format('d M Y'),
            'is_returned' => $loan->is_returned,
            'is_overdue' => $isOverdue,
            'days_overdue' => $isOverdue ? now()->diffInDays($loan->due_date) : 0,
            'days_remaining' => !$loan->is_returned && !$isOverdue ? now()->diffInDays($loan->due_date) : 0,
        ];
    }
}
