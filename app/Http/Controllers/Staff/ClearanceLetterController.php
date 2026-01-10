<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ClearanceLetter;
use Barryvdh\DomPDF\Facade\Pdf;

class ClearanceLetterController extends Controller
{
    public function download(ClearanceLetter $letter)
    {
        if ($letter->status !== 'approved') {
            abort(404, 'Surat belum disetujui');
        }
        
        $letter->load([
            'member' => fn($q) => $q->withoutGlobalScope('branch'),
            'thesisSubmission', 
            'thesisSubmission.department', 
            'thesisSubmission.department.faculty', 
            'approver'
        ]);
        
        $logoPath = public_path('storage/logo.png');
        $logoBase64 = file_exists($logoPath) 
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) 
            : null;
        
        $pdf = Pdf::loadView('pdf.clearance-letter', [
            'letter' => $letter,
            'memberSignatureQr' => $letter->generateMemberSignatureQr(),
            'approverSignatureQr' => $letter->generateApproverSignatureQr(),
            'logoBase64' => $logoBase64,
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Surat_Bebas_Pustaka_' . str_replace('/', '-', $letter->letter_number) . '.pdf');
    }
}
