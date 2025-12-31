<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ClearanceLetter;
use Illuminate\Http\Request;

class ClearanceController extends BaseController
{
    public function index(Request $request)
    {
        $letters = ClearanceLetter::where('member_id', $request->user()->id)
            ->with('thesisSubmission')
            ->orderByDesc('created_at')
            ->get();

        return $this->success($letters->map(fn($l) => $this->formatLetter($l)));
    }

    public function show(Request $request, $id)
    {
        $letter = ClearanceLetter::where('member_id', $request->user()->id)->find($id);

        if (!$letter) {
            return $this->error('Surat tidak ditemukan', 404);
        }

        return $this->success($this->formatLetterDetail($letter));
    }

    public function download(Request $request, $id)
    {
        $letter = ClearanceLetter::where('member_id', $request->user()->id)
            ->where('status', 'approved')
            ->find($id);

        if (!$letter) {
            return $this->error('Surat tidak tersedia', 404);
        }

        $pdf = $letter->generatePdf();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Surat-Bebas-Pustaka-' . $letter->letter_number . '.pdf"',
        ]);
    }

    public function checkEligibility(Request $request)
    {
        $member = $request->user();

        $hasActiveLoans = $member->loans()->where('is_returned', false)->exists();
        $hasUnpaidFines = $member->fines()->where('is_paid', false)->exists();
        $hasThesisSubmission = $member->thesisSubmissions()->whereIn('status', ['approved', 'published'])->exists();

        $eligible = !$hasActiveLoans && !$hasUnpaidFines && $hasThesisSubmission;

        return $this->success([
            'eligible' => $eligible,
            'requirements' => [
                'no_active_loans' => [
                    'status' => !$hasActiveLoans,
                    'message' => $hasActiveLoans ? 'Masih ada peminjaman aktif' : 'Tidak ada peminjaman aktif',
                ],
                'no_unpaid_fines' => [
                    'status' => !$hasUnpaidFines,
                    'message' => $hasUnpaidFines ? 'Masih ada denda belum dibayar' : 'Tidak ada denda belum dibayar',
                ],
                'has_thesis_submission' => [
                    'status' => $hasThesisSubmission,
                    'message' => $hasThesisSubmission ? 'Sudah mengajukan karya ilmiah' : 'Belum mengajukan karya ilmiah',
                ],
            ],
        ]);
    }

    protected function formatLetter(ClearanceLetter $l): array
    {
        return [
            'id' => $l->id,
            'letter_number' => $l->letter_number,
            'purpose' => $l->purpose,
            'status' => $l->status,
            'status_label' => ucfirst($l->status),
            'approved_at' => $l->approved_at?->toIso8601String(),
            'thesis_title' => $l->thesisSubmission?->title,
            'download_url' => $l->status === 'approved' ? "/api/v1/clearance/{$l->id}/download" : null,
            'created_at' => $l->created_at?->toIso8601String(),
        ];
    }

    protected function formatLetterDetail(ClearanceLetter $l): array
    {
        $data = $this->formatLetter($l);
        $data['notes'] = $l->notes;
        $data['approved_by'] = $l->approver?->name;
        return $data;
    }
}
