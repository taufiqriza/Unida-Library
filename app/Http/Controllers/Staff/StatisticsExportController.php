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
            'ebooks' => $this->exportEbooks($branchId),
            'ethesis' => $this->exportEthesis($branchId),
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

    protected function exportEbooks(?int $branchId)
    {
        $filename = 'daftar-ebook-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($branchId) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, ['No', 'Judul', 'Pengarang', 'Penerbit', 'Tahun', 'ISBN', 'Sumber', 'Kategori', 'Format', 'Akses', 'Views', 'Downloads']);
            
            $query = \App\Models\Ebook::query()
                ->with(['publisher', 'authors', 'digitalCategory'])
                ->where('is_active', true)
                ->orderBy('title');

            $no = 1;
            $query->chunk(500, function ($ebooks) use ($handle, &$no) {
                foreach ($ebooks as $ebook) {
                    $source = match($ebook->file_source) {
                        'kubuku' => 'Kubuku',
                        'google_drive' => 'Google Drive',
                        'local' => 'Lokal',
                        default => $ebook->file_source ?? 'Lokal',
                    };
                    fputcsv($handle, [
                        $no++,
                        $ebook->title,
                        $ebook->authors->pluck('name')->implode('; '),
                        $ebook->publisher?->name ?? '-',
                        $ebook->publish_year ?? '-',
                        $ebook->isbn ?? '-',
                        $source,
                        $ebook->digitalCategory?->name ?? '-',
                        strtoupper($ebook->file_format ?? 'PDF'),
                        $ebook->access_type == 'open' ? 'Terbuka' : 'Terbatas',
                        $ebook->view_count ?? 0,
                        $ebook->download_count ?? 0,
                    ]);
                }
            });
            
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function exportEthesis(?int $branchId)
    {
        $filename = 'daftar-ethesis-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($branchId) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, ['No', 'Judul', 'Penulis', 'NIM', 'Prodi', 'Jenis', 'Tahun', 'Pembimbing 1', 'Pembimbing 2', 'Sumber', 'Fulltext', 'Views', 'Downloads']);
            
            $query = \App\Models\Ethesis::query()
                ->with(['department'])
                ->where('is_public', true)
                ->orderByDesc('year')
                ->orderBy('title');

            $no = 1;
            $query->chunk(500, function ($theses) use ($handle, &$no) {
                foreach ($theses as $thesis) {
                    $source = match($thesis->source_type) {
                        'eprints' => 'EPrints Repository',
                        'local' => 'Sistem Lokal',
                        'submission' => 'Upload Mahasiswa',
                        default => $thesis->source_type ?? 'Lokal',
                    };
                    fputcsv($handle, [
                        $no++,
                        $thesis->title,
                        $thesis->author,
                        $thesis->nim ?? '-',
                        $thesis->department?->name ?? '-',
                        $thesis->getTypeLabel(),
                        $thesis->year,
                        $thesis->advisor1 ?? '-',
                        $thesis->advisor2 ?? '-',
                        $source,
                        $thesis->is_fulltext_public ? 'Ya' : 'Tidak',
                        $thesis->views ?? 0,
                        $thesis->downloads ?? 0,
                    ]);
                }
            });
            
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
