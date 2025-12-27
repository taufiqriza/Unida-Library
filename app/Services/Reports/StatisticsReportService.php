<?php

namespace App\Services\Reports;

use App\Models\Branch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class StatisticsReportService
{
    protected array $stats;
    protected array $collectionData;
    protected array $circulationData;
    protected ?int $branchId;
    protected ?Branch $branch;
    protected string $generatedAt;
    protected string $generatedBy;

    public function __construct(array $stats, array $collectionData, array $circulationData, ?int $branchId = null)
    {
        $this->stats = $stats;
        $this->collectionData = $collectionData;
        $this->circulationData = $circulationData;
        $this->branchId = $branchId;
        $this->branch = $branchId ? Branch::find($branchId) : null;
        $this->generatedAt = Carbon::now()->format('d F Y, H:i');
        $this->generatedBy = auth()->user()->name ?? 'System';
    }

    public function exportOverview()
    {
        $pdf = Pdf::loadView('reports.statistics.overview', $this->getCommonData());
        return $pdf->setPaper('A4', 'portrait')->download($this->filename('ringkasan-umum'));
    }

    public function exportCollection()
    {
        $data = array_merge($this->getCommonData(), [
            'byClassification' => $this->sanitizeArray($this->collectionData['byClassification']),
            'byMediaType' => $this->sanitizeArray($this->collectionData['byMediaType']),
            'byCollectionType' => $this->sanitizeArray($this->collectionData['byCollectionType']),
            'byLanguage' => $this->sanitizeArray($this->collectionData['byLanguage']),
            'byPublisher' => $this->sanitizeArray($this->collectionData['byPublisher']),
            'byYear' => $this->sanitizeArray($this->collectionData['byYear']),
            'bySubject' => $this->sanitizeArray($this->collectionData['bySubject']),
            'byAuthor' => $this->sanitizeArray($this->collectionData['byAuthor']),
        ]);
        
        $pdf = Pdf::loadView('reports.statistics.collection', $data);
        return $pdf->setPaper('A4', 'portrait')->download($this->filename('analisis-koleksi'));
    }

    public function exportCirculation()
    {
        $data = array_merge($this->getCommonData(), [
            'monthlyTrend' => $this->sanitizeArray($this->circulationData['monthlyTrend']),
            'branchStats' => $this->sanitizeArray($this->circulationData['branchStats']),
        ]);
        
        $pdf = Pdf::loadView('reports.statistics.circulation', $data);
        return $pdf->setPaper('A4', 'portrait')->download($this->filename('sirkulasi-anggota'));
    }

    public function exportFull()
    {
        $data = array_merge($this->getCommonData(), [
            'byClassification' => $this->sanitizeArray($this->collectionData['byClassification']),
            'byMediaType' => $this->sanitizeArray($this->collectionData['byMediaType']),
            'byCollectionType' => $this->sanitizeArray($this->collectionData['byCollectionType']),
            'byLanguage' => $this->sanitizeArray($this->collectionData['byLanguage']),
            'byPublisher' => $this->sanitizeArray($this->collectionData['byPublisher']),
            'byYear' => $this->sanitizeArray($this->collectionData['byYear']),
            'bySubject' => $this->sanitizeArray($this->collectionData['bySubject']),
            'byAuthor' => $this->sanitizeArray($this->collectionData['byAuthor']),
            'monthlyTrend' => $this->sanitizeArray($this->circulationData['monthlyTrend']),
            'branchStats' => $this->sanitizeArray($this->circulationData['branchStats']),
        ]);
        
        $pdf = Pdf::loadView('reports.statistics.full', $data);
        return $pdf->setPaper('A4', 'portrait')->download($this->filename('laporan-lengkap'));
    }

    protected function getCommonData(): array
    {
        return [
            'stats' => $this->sanitizeArray($this->stats),
            'branch' => $this->branch,
            'branchName' => $this->branch?->name ?? 'Seluruh Cabang',
            'generatedAt' => $this->generatedAt,
            'generatedBy' => $this->generatedBy,
            'reportPeriod' => Carbon::now()->format('F Y'),
            'logoBase64' => $this->getCompressedLogo(),
        ];
    }

    protected function getCompressedLogo(): ?string
    {
        $logoPath = public_path('storage/logo-portal.png');
        if (!file_exists($logoPath)) {
            $logoPath = storage_path('app/public/logo-portal.png');
            if (!file_exists($logoPath)) return null;
        }

        if (function_exists('imagecreatefrompng')) {
            $img = @imagecreatefrompng($logoPath);
            if ($img) {
                $w = imagesx($img);
                $h = imagesy($img);
                $newW = 100;
                $newH = (int)($h * $newW / $w);
                $thumb = imagecreatetruecolor($newW, $newH);
                imagesavealpha($thumb, true);
                $trans = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
                imagefill($thumb, 0, 0, $trans);
                imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
                ob_start();
                imagepng($thumb, null, 9);
                $data = ob_get_clean();
                imagedestroy($img);
                imagedestroy($thumb);
                return 'data:image/png;base64,' . base64_encode($data);
            }
        }
        
        if (filesize($logoPath) > 100000) return null;
        return 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }

    protected function sanitizeArray(array $data): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return $this->sanitizeArray($value);
            }
            if (is_string($value)) {
                return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
            return $value;
        }, $data);
    }

    protected function filename(string $type): string
    {
        $branch = $this->branch ? '-' . str($this->branch->code ?? $this->branch->name)->slug() : '';
        $date = Carbon::now()->format('Y-m-d');
        return "laporan-{$type}{$branch}-{$date}.pdf";
    }
}
