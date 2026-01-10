<?php

namespace App\Http\Controllers\Opac;

use App\Http\Controllers\Controller;
use App\Models\ClearanceLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ClearanceLetterController extends Controller
{
    public function download(ClearanceLetter $letter)
    {
        $member = Auth::guard('member')->user();
        
        if ($letter->member_id !== $member->id) {
            abort(403, 'Akses ditolak');
        }
        
        if ($letter->status !== 'approved') {
            abort(404, 'Surat belum disetujui');
        }
        
        $letter->load(['member', 'thesisSubmission', 'thesisSubmission.department', 'thesisSubmission.department.faculty', 'approver']);
        
        $memberSignatureQr = $letter->generateMemberSignatureQr();
        $approverSignatureQr = $letter->generateApproverSignatureQr();
        
        // Compress logo to base64 (resize if too large)
        $logoBase64 = $this->getCompressedLogo();
        
        $pdf = Pdf::loadView('pdf.clearance-letter', [
            'letter' => $letter,
            'memberSignatureQr' => $memberSignatureQr,
            'approverSignatureQr' => $approverSignatureQr,
            'logoBase64' => $logoBase64,
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Surat_Bebas_Pustaka_' . str_replace('/', '-', $letter->letter_number) . '.pdf';
        
        return $pdf->download($filename);
    }

    private function getCompressedLogo(): ?string
    {
        $logoPath = public_path('storage/logo.png');
        if (!file_exists($logoPath)) return null;

        return 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
}
