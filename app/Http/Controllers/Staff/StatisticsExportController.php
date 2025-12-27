<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Reports\StatisticsReportService;
use App\Livewire\Staff\Statistics\LibraryStatistics;
use Illuminate\Http\Request;

class StatisticsExportController extends Controller
{
    public function export(Request $request, string $type)
    {
        $user = auth()->user();
        
        // Non super_admin always use their own branch
        if ($user->role !== 'super_admin') {
            $branchId = $user->branch_id;
        } else {
            // Super admin must select a branch
            $branchId = $request->get('branch');
            if (!$branchId) {
                return back()->with('error', 'Silakan pilih cabang terlebih dahulu');
            }
        }

        // Get statistics data
        $component = new LibraryStatistics();
        $component->selectedBranch = $branchId;
        $component->loadStatistics();

        $service = new StatisticsReportService(
            $component->stats,
            [
                'byClassification' => $component->byClassification,
                'byMediaType' => $component->byMediaType,
                'byCollectionType' => $component->byCollectionType,
                'byLanguage' => $component->byLanguage,
                'byPublisher' => $component->byPublisher,
                'byYear' => $component->byYear,
                'bySubject' => $component->bySubject,
                'byAuthor' => $component->byAuthor,
                'byDepartment' => $component->byDepartment,
                'byInputYear' => $component->byInputYear,
            ],
            [
                'monthlyTrend' => $component->monthlyTrend,
                'branchStats' => $component->branchStats,
            ],
            $branchId
        );

        return match($type) {
            'overview' => $service->exportOverview(),
            'collection' => $service->exportCollection(),
            'circulation' => $service->exportCirculation(),
            'full' => $service->exportFull(),
            'catalog' => $this->exportCatalog($branchId),
            default => abort(404),
        };
    }

    protected function exportCatalog(?int $branchId)
    {
        $query = \App\Models\Book::query()
            ->withoutGlobalScope('branch')
            ->with(['publisher', 'items'])
            ->select('id', 'title', 'publish_place', 'publisher_id', 'isbn', 'call_number', 'classification')
            ->orderBy('title');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $books = $query->get()->map(function ($book) {
            return [
                'title' => $book->title,
                'copies' => $book->items->count(),
                'publish_place' => $book->publish_place,
                'publisher' => $book->publisher?->name,
                'isbn' => $book->isbn,
                'call_number' => $book->call_number ?? $book->classification,
            ];
        })->toArray();

        $branch = $branchId ? \App\Models\Branch::find($branchId) : null;
        $branchName = $branch?->name ?? 'Seluruh Cabang';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.statistics.catalog', [
            'books' => $books,
            'branchName' => $branchName,
            'generatedAt' => now()->format('d F Y, H:i'),
            'generatedBy' => auth()->user()->name ?? 'System',
            'totalTitles' => count($books),
            'totalCopies' => array_sum(array_column($books, 'copies')),
            'logoBase64' => $this->getCompressedLogo(),
        ]);

        $filename = 'daftar-koleksi-' . ($branch ? str($branch->code ?? $branch->name)->slug() . '-' : '') . now()->format('Y-m-d') . '.pdf';
        return $pdf->setPaper('A4', 'landscape')->download($filename);
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
}
