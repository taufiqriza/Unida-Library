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
            default => abort(404),
        };
    }
}
