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
            // Super admin must select a branch - validate it exists
            $branchId = $request->get('branch');
            if (!$branchId || !\App\Models\Branch::where('id', $branchId)->where('is_active', true)->exists()) {
                return back()->with('error', 'Silakan pilih cabang yang valid');
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
            'ebooks' => $this->exportEbooks(),
            'ethesis' => $this->exportEthesis(),
            default => abort(404),
        };
    }

    protected function exportCatalog(?int $branchId)
    {
        $branch = $branchId ? \App\Models\Branch::find($branchId) : null;
        $branchName = $branch?->name ?? 'Semua-Cabang';
        $filename = 'daftar-koleksi-' . str($branchName)->slug() . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($branchId) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            // Header
            fputcsv($handle, ['No', 'Judul', 'Salin', 'Tempat Terbit', 'Penerbit', 'ISBN/ISSN', 'No. Panggil']);
            
            $query = \App\Models\Book::query()
                ->withoutGlobalScope('branch')
                ->with(['publisher'])
                ->withCount('items')
                ->select('id', 'title', 'publish_place', 'publisher_id', 'isbn', 'call_number', 'classification')
                ->orderBy('title');

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $no = 1;
            $query->chunk(500, function ($books) use ($handle, &$no) {
                foreach ($books as $book) {
                    fputcsv($handle, [
                        $no++,
                        $book->title,
                        $book->items_count,
                        $book->publish_place ?? '-',
                        $book->publisher?->name ?? '-',
                        $book->isbn ?? '-',
                        $book->call_number ?? $book->classification ?? '-',
                    ]);
                }
            });
            
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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

    protected function exportEbooks()
    {
        // Get summary statistics
        $totalEbooks = \App\Models\Ebook::where('is_active', true)->count();
        $bySource = \App\Models\Ebook::where('is_active', true)
            ->selectRaw("COALESCE(file_source, 'local') as source, COUNT(*) as count")
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();
        
        // Kubuku count (from external API - stored separately or check collection_type)
        $kubukuCount = \App\Models\Ebook::where('is_active', true)
            ->where(function($q) {
                $q->where('file_source', 'kubuku')
                  ->orWhere('collection_type', 'kubuku');
            })->count();
        
        // Shamela count from local SQLite database
        $shamelaCount = app(\App\Services\ShamelaLocalService::class)->getStats()['total_books'] ?? 0;
        
        // Open Library is external API, no local count
        
        $byCategory = \App\Models\Ebook::where('is_active', true)
            ->join('digital_categories', 'ebooks.digital_category_id', '=', 'digital_categories.id')
            ->selectRaw('digital_categories.name, COUNT(*) as count')
            ->groupBy('digital_categories.name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'name')
            ->toArray();

        $byYear = \App\Models\Ebook::where('is_active', true)
            ->whereNotNull('publish_year')
            ->selectRaw('publish_year, COUNT(*) as count')
            ->groupBy('publish_year')
            ->orderByDesc('publish_year')
            ->limit(10)
            ->pluck('count', 'publish_year')
            ->toArray();

        $totalViews = \App\Models\Ebook::where('is_active', true)->sum('view_count');
        $totalDownloads = \App\Models\Ebook::where('is_active', true)->sum('download_count');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.statistics.ebooks', [
            'totalEbooks' => $totalEbooks,
            'bySource' => $bySource,
            'kubukuCount' => $kubukuCount,
            'shamelaCount' => $shamelaCount,
            'byCategory' => $byCategory,
            'byYear' => $byYear,
            'totalViews' => $totalViews,
            'totalDownloads' => $totalDownloads,
            'generatedAt' => now()->format('d F Y, H:i'),
            'generatedBy' => auth()->user()->name ?? 'System',
            'logoBase64' => $this->getCompressedLogo(),
        ]);

        return $pdf->setPaper('A4', 'portrait')->download('laporan-ebook-' . now()->format('Y-m-d') . '.pdf');
    }

    protected function exportEthesis()
    {
        $totalEthesis = \App\Models\Ethesis::where('is_public', true)->count();
        
        $bySource = \App\Models\Ethesis::where('is_public', true)
            ->selectRaw("COALESCE(source_type, 'local') as source, COUNT(*) as count")
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();

        $byType = \App\Models\Ethesis::where('is_public', true)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderByDesc('count')
            ->pluck('count', 'type')
            ->toArray();

        $byDepartment = \App\Models\Ethesis::where('is_public', true)
            ->join('departments', 'etheses.department_id', '=', 'departments.id')
            ->selectRaw('departments.name, COUNT(*) as count')
            ->groupBy('departments.name')
            ->orderByDesc('count')
            ->limit(15)
            ->pluck('count', 'name')
            ->toArray();

        $byYear = \App\Models\Ethesis::where('is_public', true)
            ->whereNotNull('year')
            ->selectRaw('year, COUNT(*) as count')
            ->groupBy('year')
            ->orderByDesc('year')
            ->limit(10)
            ->pluck('count', 'year')
            ->toArray();

        $fulltextPublic = \App\Models\Ethesis::where('is_public', true)->where('is_fulltext_public', true)->count();
        $totalViews = \App\Models\Ethesis::where('is_public', true)->sum('views');
        $totalDownloads = \App\Models\Ethesis::where('is_public', true)->sum('downloads');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.statistics.ethesis', [
            'totalEthesis' => $totalEthesis,
            'bySource' => $bySource,
            'byType' => $byType,
            'byDepartment' => $byDepartment,
            'byYear' => $byYear,
            'fulltextPublic' => $fulltextPublic,
            'totalViews' => $totalViews,
            'totalDownloads' => $totalDownloads,
            'generatedAt' => now()->format('d F Y, H:i'),
            'generatedBy' => auth()->user()->name ?? 'System',
            'logoBase64' => $this->getCompressedLogo(),
        ]);

        return $pdf->setPaper('A4', 'portrait')->download('laporan-ethesis-' . now()->format('Y-m-d') . '.pdf');
    }
}
