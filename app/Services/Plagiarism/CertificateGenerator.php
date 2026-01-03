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

        // Generate signature QR for head librarian
        $headLibrarian = Setting::get('plagiarism_head_librarian', 'Kepala Perpustakaan');
        $signatureData = "DIGITAL SIGNATURE\n{$headLibrarian}\nKepala Perpustakaan\n" . now()->format('Y-m-d H:i:s');
        $signatureQr = $this->generateQrCode($signatureData);

        // Check if title or name contains Arabic
        $hasArabicTitle = preg_match('/[\x{0600}-\x{06FF}]/u', $this->check->document_title);
        $hasArabicName = preg_match('/[\x{0600}-\x{06FF}]/u', $this->check->member->name);

        // Prepare data for certificate
        $data = [
            'check' => $this->check,
            'member' => $this->check->member,
            'qrCode' => $qrCode,
            'verifyUrl' => $verifyUrl,
            'institutionName' => Setting::get('app_name', 'Perpustakaan UNIDA Gontor'),
            'institutionLogo' => $this->getStorageLogoBase64(),
            'headLibrarian' => $headLibrarian,
            'issuedDate' => now()->translatedFormat('d F Y'),
            'isPassed' => $this->check->isPassed(),
            'passThreshold' => (float) Setting::get('plagiarism_pass_threshold', 25),
            // Logos from public/images/certificates/
            'logoIthenticate' => $this->getAssetBase64('images/certificates/logo-ithenticate.png'),
            'signatureQr' => $signatureQr,
            'downloadQr' => $downloadQr,
            'hasArabicTitle' => $hasArabicTitle,
            'hasArabicName' => $hasArabicName,
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
     * Get logo directly from storage public folder
     */
    protected function getStorageLogoBase64(): ?string
    {
        // Try logo-portal.png first (same as staff portal)
        $portalLogoPath = storage_path('app/public/logo-portal.png');
        if (file_exists($portalLogoPath)) {
            $content = file_get_contents($portalLogoPath);
            $mimeType = mime_content_type($portalLogoPath);
            return 'data:' . $mimeType . ';base64,' . base64_encode($content);
        }

        // Fallback to logo.png
        $fixedPath = storage_path('app/public/logo.png');
        if (file_exists($fixedPath)) {
            $content = file_get_contents($fixedPath);
            $mimeType = mime_content_type($fixedPath);
            return 'data:' . $mimeType . ';base64,' . base64_encode($content);
        }

        // Fallback to setting
        return $this->getLogoBase64();
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
     * Always regenerate to ensure latest template and proper Arabic font rendering
     */
    public function getPdfContent(): string
    {
        // Always regenerate for fresh download with proper fonts
        $this->generate();
        $this->check->refresh();

        return Storage::disk('local')->get($this->check->certificate_path) ?? '';
    }

    /**
     * Get the certificate download filename
     */
    public function getDownloadFilename(): string
    {
        return 'Sertifikat-Plagiasi-' . $this->check->certificate_number . '.pdf';
    }
}
