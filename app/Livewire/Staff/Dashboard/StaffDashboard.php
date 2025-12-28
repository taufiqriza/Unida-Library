<?php

namespace App\Livewire\Staff\Dashboard;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Support\Carbon;
use Livewire\Component;

class StaffDashboard extends Component
{
    public array $stats = [];
    public array $chartData = [];

    public function mount()
    {
        $this->loadData();
    }

    protected function getBranchId(): ?int
    {
        $user = auth()->user();
        return $user->branch_id ?? session('staff_branch_id') ?? 1;
    }

    public function loadData()
    {
        $branchId = $this->getBranchId();

        // Cache stats for 2 minutes
        $this->stats = cache()->remember("dashboard_stats_{$branchId}", 120, function () use ($branchId) {
            $startOfMonth = now()->startOfMonth();
            return [
                'loans_month' => Loan::where('branch_id', $branchId)->where('loan_date', '>=', $startOfMonth)->count(),
                'returns_month' => Loan::where('branch_id', $branchId)->where('return_date', '>=', $startOfMonth)->count(),
                'overdue' => Loan::where('branch_id', $branchId)->where('is_returned', false)->where('due_date', '<', now())->count(),
                'unpaid_fines' => Fine::whereHas('loan', fn($q) => $q->where('branch_id', $branchId))->where('is_paid', false)->sum('amount'),
                'total_books' => Book::where('branch_id', $branchId)->count(),
                'total_items' => Item::where('branch_id', $branchId)->count(),
                'total_members' => Member::where('branch_id', $branchId)->count(),
                'active_loans' => Loan::where('branch_id', $branchId)->where('is_returned', false)->count(),
            ];
        });

        // Cache chart data for 10 minutes
        $this->chartData = cache()->remember("dashboard_chart_{$branchId}", 600, function () use ($branchId) {
            return $this->getChartData($branchId);
        });
    }

    public function getRecentLoansProperty()
    {
        $branchId = $this->getBranchId();
        
        return cache()->remember("recent_loans_{$branchId}", 60, function () use ($branchId) {
            return Loan::query()
                ->select(['id', 'member_id', 'item_id', 'loan_date', 'due_date', 'is_returned'])
                ->with([
                    'member:id,name',
                    'item:id,book_id,barcode',
                    'item.book:id,title'
                ])
                ->where('branch_id', $branchId)
                ->latest('loan_date')
                ->limit(10)
                ->get();
        });
    }

    public function getOverdueLoansProperty()
    {
        $branchId = $this->getBranchId();
        
        return cache()->remember("overdue_loans_{$branchId}", 60, function () use ($branchId) {
            return Loan::query()
                ->select(['id', 'member_id', 'item_id', 'loan_date', 'due_date'])
                ->with([
                    'member:id,name',
                    'item:id,book_id,barcode',
                    'item.book:id,title'
                ])
                ->where('branch_id', $branchId)
                ->where('is_returned', false)
                ->where('due_date', '<', now())
                ->orderBy('due_date')
                ->limit(5)
                ->get();
        });
    }

    protected function getChartData($branchId): array
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        // Single query for daily data
        $dailyLoans = Loan::where('branch_id', $branchId)
            ->whereBetween('loan_date', [$startDate, $endDate])
            ->selectRaw('DATE(loan_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();
            
        $dailyReturns = Loan::where('branch_id', $branchId)
            ->whereBetween('return_date', [$startDate, $endDate])
            ->selectRaw('DATE(return_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $dailyLabels = [];
        $dailyLoanCounts = [];
        $dailyReturnCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $dailyLabels[] = $date->format('d M');
            $dailyLoanCounts[] = $dailyLoans[$dateKey] ?? 0;
            $dailyReturnCounts[] = $dailyReturns[$dateKey] ?? 0;
        }

        // Single query for monthly data
        $monthlyLoans = Loan::where('branch_id', $branchId)
            ->whereYear('loan_date', now()->year)
            ->selectRaw('MONTH(loan_date) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyLabels = [];
        $monthlyData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyLabels[] = Carbon::create(null, $i, 1)->format('M');
            $monthlyData[] = $monthlyLoans[$i] ?? 0;
        }

        return [
            'daily' => [
                'labels' => $dailyLabels,
                'loans' => $dailyLoanCounts,
                'returns' => $dailyReturnCounts,
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'totals' => $monthlyData,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.staff.dashboard.staff-dashboard', [
            'recentLoans' => $this->recentLoans,
            'overdueLoans' => $this->overdueLoans,
        ])->extends('staff.layouts.app')->section('content');
    }
}
