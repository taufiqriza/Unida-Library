<?php

namespace App\Services\Plagiarism;

use App\Models\PlagiarismCheck;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateGenerator
{
    protected PlagiarismCheck $check;

    public function __construct(PlagiarismCheck $check)
    {
        $this->check = $check;
    }

    /**
     * Generate certificate PDF
     */
    public function generate(): string
    {
        // Generate certificate number if not exists
        if (!$this->check->certificate_number) {
            $this->check->certificate_number = PlagiarismCheck::generateCertificateNumber();
        }

        // Generate QR code for verification
        $verifyUrl = route('plagiarism.verify', $this->check->certificate_number);
        $qrCode = $this->generateQrCode($verifyUrl);

        // Generate download QR if external report URL exists
        $downloadQr = null;
        if ($this->check->external_report_url) {
            $downloadQr = $this->generateQrCode($this->check->external_report_url);
        }

        // Prepare data for certificate
        $data = [
            'check' => $this->check,
            'member' => $this->check->member,
            'qrCode' => $qrCode,
            'verifyUrl' => $verifyUrl,
            'institutionName' => Setting::get('app_name', 'Perpustakaan UNIDA Gontor'),
            'institutionLogo' => $this->getLogoBase64(),
            'headLibrarian' => Setting::get('plagiarism_head_librarian', 'Kepala Perpustakaan'),
            'issuedDate' => now()->translatedFormat('d F Y'),
            'isPassed' => $this->check->isPassed(),
            'passThreshold' => (float) Setting::get('plagiarism_pass_threshold', 25),
            // Additional logos for the certificate design
            'logoGontor' => $this->getAssetBase64('images/certificates/logo-gontor.png'),
            'logoUnida' => $this->getAssetBase64('images/certificates/logo-unida.png'),
            'logoAkreditasi' => $this->getAssetBase64('images/certificates/logo-akreditasi.png'),
            'logoIthenticate' => $this->getAssetBase64('images/certificates/logo-ithenticate.png'),
            'logoTurnitin' => $this->getAssetBase64('images/certificates/logo-turnitin.png'),
            'badgeAccreditation' => $this->getAssetBase64('images/certificates/badge-unggul.png'),
            'signatureQr' => null, // Can be added later for digital signature
            'downloadQr' => $downloadQr,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('certificates.plagiarism', $data);
        $pdf->setPaper('a4', 'portrait');
        
        // Save to storage
        $filename = 'certificates/' . $this->check->certificate_number . '.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        // Update check record
        $this->check->update([
            'certificate_number' => $this->check->certificate_number,
            'certificate_path' => $filename,
            'certificate_generated_at' => now(),
        ]);

        return $filename;
    }

    /**
     * Generate QR code as base64
     */
    protected function generateQrCode(string $url): string
    {
        $qr = QrCode::format('svg')
            ->size(150)
            ->margin(1)
            ->generate($url);

        return 'data:image/svg+xml;base64,' . base64_encode($qr);
    }

    /**
     * Get institution logo as base64
     */
    protected function getLogoBase64(): ?string
    {
        $logoPath = Setting::get('app_logo');
        
        if (!$logoPath || !Storage::disk('public')->exists($logoPath)) {
            return null;
        }

        $logoContent = Storage::disk('public')->get($logoPath);
        $mimeType = Storage::disk('public')->mimeType($logoPath);

        return 'data:' . $mimeType . ';base64,' . base64_encode($logoContent);
    }

    /**
     * Get asset from public folder as base64
     */
    protected function getAssetBase64(string $path): ?string
    {
        $fullPath = public_path($path);
        
        if (!file_exists($fullPath)) {
            return null;
        }

        $content = file_get_contents($fullPath);
        $mimeType = mime_content_type($fullPath);

        return 'data:' . $mimeType . ';base64,' . base64_encode($content);
    }

    /**
     * Get the PDF content for download
     */
    public function getPdfContent(): string
    {
        if (!$this->check->certificate_path) {
            $this->generate();
        }

        return Storage::disk('local')->get($this->check->certificate_path);
    }

    /**
     * Get the certificate download filename
     */
    public function getDownloadFilename(): string
    {
        return 'Sertifikat-Plagiasi-' . $this->check->certificate_number . '.pdf';
    }
}
