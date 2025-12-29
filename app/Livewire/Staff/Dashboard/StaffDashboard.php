<?php

namespace App\Livewire\Staff\Dashboard;

use App\Models\Book;
use App\Models\Branch;
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
    public ?int $selectedBranchId = null;

    public function mount()
    {
        $user = auth()->user();
        // Super admin default: null (semua cabang), others: their branch
        $this->selectedBranchId = $user->role === 'super_admin' ? null : $user->branch_id;
        $this->loadData();
    }

    public function updatedSelectedBranchId()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = auth()->user();
        $branchId = $user->role === 'super_admin' ? $this->selectedBranchId : $user->branch_id;
        $cacheKey = $branchId ?? 'all';

        // Cache stats for 2 minutes
        $this->stats = cache()->remember("dashboard_stats_{$cacheKey}", 120, function () use ($branchId) {
            $startOfMonth = now()->startOfMonth();
            
            $loanQuery = Loan::query();
            $bookQuery = Book::query();
            $itemQuery = Item::query();
            $memberQuery = Member::query();
            $fineQuery = Fine::query();
            
            if ($branchId) {
                $loanQuery->where('branch_id', $branchId);
                $bookQuery->where('branch_id', $branchId);
                $itemQuery->where('branch_id', $branchId);
                $memberQuery->where('branch_id', $branchId);
                $fineQuery->whereHas('loan', fn($q) => $q->where('branch_id', $branchId));
            }
            
            return [
                'loans_month' => (clone $loanQuery)->where('loan_date', '>=', $startOfMonth)->count(),
                'returns_month' => (clone $loanQuery)->whereNotNull('return_date')->where('return_date', '>=', $startOfMonth)->count(),
                'overdue' => (clone $loanQuery)->where('is_returned', false)->where('due_date', '<', now())->count(),
                'unpaid_fines' => (clone $fineQuery)->where('is_paid', false)->sum('amount'),
                'total_books' => $bookQuery->count(),
                'total_items' => $itemQuery->count(),
                'total_members' => $memberQuery->count(),
                'active_loans' => (clone $loanQuery)->where('is_returned', false)->count(),
            ];
        });

        // Cache chart data for 10 minutes
        $this->chartData = cache()->remember("dashboard_chart_{$cacheKey}", 600, function () use ($branchId) {
            return $this->getChartData($branchId);
        });
    }

    protected function getCurrentBranchId(): ?int
    {
        $user = auth()->user();
        return $user->role === 'super_admin' ? $this->selectedBranchId : $user->branch_id;
    }

    public function getRecentLoansProperty()
    {
        $branchId = $this->getCurrentBranchId();
        $cacheKey = $branchId ?? 'all';
        
        return cache()->remember("recent_loans_{$cacheKey}", 60, function () use ($branchId) {
            $query = Loan::query()
                ->select(['id', 'member_id', 'item_id', 'loan_date', 'due_date', 'is_returned', 'branch_id'])
                ->with([
                    'member:id,name',
                    'item:id,book_id,barcode',
                    'item.book:id,title',
                    'branch:id,name'
                ]);
            
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            
            return $query->latest('loan_date')->limit(10)->get();
        });
    }

    public function getOverdueLoansProperty()
    {
        $branchId = $this->getCurrentBranchId();
        $cacheKey = $branchId ?? 'all';
        
        return cache()->remember("overdue_loans_{$cacheKey}", 60, function () use ($branchId) {
            $query = Loan::query()
                ->select(['id', 'member_id', 'item_id', 'loan_date', 'due_date', 'branch_id'])
                ->with([
                    'member:id,name',
                    'item:id,book_id,barcode',
                    'item.book:id,title',
                    'branch:id,name'
                ])
                ->where('is_returned', false)
                ->where('due_date', '<', now());
            
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            
            return $query->orderBy('due_date')->limit(5)->get();
        });
    }

    protected function getChartData($branchId): array
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        // Single query for daily data
        $loanQuery = Loan::query();
        if ($branchId) {
            $loanQuery->where('branch_id', $branchId);
        }
        
        $dailyLoans = (clone $loanQuery)
            ->whereBetween('loan_date', [$startDate, $endDate])
            ->selectRaw('DATE(loan_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();
            
        $dailyReturns = (clone $loanQuery)
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
        $monthlyLoans = (clone $loanQuery)
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
        $user = auth()->user();
        $branches = $user->role === 'super_admin' ? Branch::orderBy('name')->get() : collect();
        
        return view('livewire.staff.dashboard.staff-dashboard', [
            'recentLoans' => $this->recentLoans,
            'overdueLoans' => $this->overdueLoans,
            'branches' => $branches,
            'isSuperAdmin' => $user->role === 'super_admin',
        ])->extends('staff.layouts.app')->section('content');
    }
}
