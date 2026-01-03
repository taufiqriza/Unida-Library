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
    protected static array $logoCache = [];

    public function __construct(PlagiarismCheck $check)
    {
        $this->check = $check;
    }

    public function generate(): string
    {
        if (!$this->check->certificate_number) {
            $this->check->certificate_number = PlagiarismCheck::generateCertificateNumber();
        }

        $verifyUrl = route('plagiarism.verify', $this->check->certificate_number);
        $headLibrarian = Setting::get('plagiarism_head_librarian', 'Kepala Perpustakaan');

        $data = [
            'check' => $this->check,
            'member' => $this->check->member,
            'qrCode' => $this->generateQrCode($verifyUrl, 70),
            'qrHeadLibrarian' => $this->generateQrCode($this->generateHeadLibrarianSignature(), 50),
            'verifyUrl' => $verifyUrl,
            'institutionName' => Setting::get('app_name', 'Perpustakaan UNIDA Gontor'),
            'institutionLogo' => $this->getOptimizedLogo(),
            'headLibrarian' => $headLibrarian,
            'issuedDate' => now()->translatedFormat('d F Y'),
            'isPassed' => $this->check->isPassed(),
            'passThreshold' => (float) Setting::get('plagiarism_pass_threshold', 25),
            'hasArabicTitle' => (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $this->check->document_title),
        ];

        $pdf = Pdf::loadView('certificates.plagiarism', $data);
        $pdf->setPaper('a4', 'portrait');
        $pdf->getDomPDF()->set_option('compress', true);
        
        $filename = 'certificates/' . $this->check->certificate_number . '.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        $this->check->update([
            'certificate_number' => $this->check->certificate_number,
            'certificate_path' => $filename,
            'certificate_generated_at' => now(),
        ]);

        return $filename;
    }

    protected function generateQrCode(string $data, int $size = 80): string
    {
        $qr = QrCode::format('svg')->size($size)->margin(0)->generate($data);
        return 'data:image/svg+xml;base64,' . base64_encode($qr);
    }

    protected function generateHeadLibrarianSignature(): string
    {
        return json_encode([
            'type' => 'head_librarian_signature',
            'certificate' => $this->check->certificate_number,
            'signer' => Setting::get('plagiarism_head_librarian', 'Kepala Perpustakaan'),
            'issued' => now()->format('Y-m-d H:i:s'),
            'institution' => Setting::get('app_name', 'Perpustakaan UNIDA Gontor')
        ]);
    }

    protected function getOptimizedLogo(): ?string
    {
        if (isset(self::$logoCache['main'])) {
            return self::$logoCache['main'];
        }

        // Use logo-portal.png for premium look
        $logoPath = storage_path('app/public/logo-portal.png');
        if (!file_exists($logoPath)) {
            $logoPath = storage_path('app/public/logo.png');
        }
        if (!file_exists($logoPath)) {
            return null;
        }

        $content = file_get_contents($logoPath);
        
        // Resize to max 120px height for certificate
        if (function_exists('imagecreatefromstring')) {
            $content = $this->resizeImage($content, 120);
        }

        self::$logoCache['main'] = 'data:image/png;base64,' . base64_encode($content);
        return self::$logoCache['main'];
    }

    protected function resizeImage(string $content, int $maxHeight): string
    {
        $img = @imagecreatefromstring($content);
        if (!$img) return $content;

        $w = imagesx($img);
        $h = imagesy($img);
        
        if ($h <= $maxHeight) {
            imagedestroy($img);
            return $content;
        }

        $newH = $maxHeight;
        $newW = (int) ($w * ($maxHeight / $h));
        
        $resized = imagecreatetruecolor($newW, $newH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
        
        ob_start();
        imagepng($resized, null, 6);
        $result = ob_get_clean();
        
        imagedestroy($img);
        imagedestroy($resized);
        
        return $result;
    }

    public function getPdfContent(): string
    {
        // Only regenerate if not exists
        if (!$this->check->certificate_path || !Storage::disk('local')->exists($this->check->certificate_path)) {
            $this->generate();
            $this->check->refresh();
        }

        return Storage::disk('local')->get($this->check->certificate_path) ?? '';
    }

    public function getDownloadFilename(): string
    {
        return 'Sertifikat-Plagiasi-' . $this->check->certificate_number . '.pdf';
    }

    /**
     * Force regenerate certificate (for admin use)
     */
    public function regenerate(): string
    {
        return $this->generate();
    }
}
