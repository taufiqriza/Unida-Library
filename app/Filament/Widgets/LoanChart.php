<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class LoanChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Peminjaman (7 Hari Terakhir)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = auth()->user();
        $branchId = $user->getCurrentBranchId();

        $data = collect(range(6, 0))->map(function ($daysAgo) use ($user, $branchId) {
            $date = Carbon::now()->subDays($daysAgo)->toDateString();
            $query = Loan::whereDate('loan_date', $date);
            
            if (!$user->isSuperAdmin() || $branchId) {
                $query->where('branch_id', $branchId ?? $user->branch_id);
            }
            
            return $query->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Peminjaman',
                    'data' => $data->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => collect(range(6, 0))->map(fn ($d) => Carbon::now()->subDays($d)->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
