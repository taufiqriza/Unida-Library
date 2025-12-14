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

        $this->stats = [
            'loans_today' => Loan::where('branch_id', $branchId)->whereDate('loan_date', today())->count(),
            'returns_today' => Loan::where('branch_id', $branchId)->whereDate('return_date', today())->count(),
            'overdue' => Loan::where('branch_id', $branchId)->where('is_returned', false)->where('due_date', '<', now())->count(),
            'unpaid_fines' => Fine::whereHas('loan', fn($q) => $q->where('branch_id', $branchId))->where('is_paid', false)->sum('amount'),
            'total_books' => Book::where('branch_id', $branchId)->count(),
            'total_items' => Item::where('branch_id', $branchId)->count(),
            'total_members' => Member::where('branch_id', $branchId)->count(),
            'active_loans' => Loan::where('branch_id', $branchId)->where('is_returned', false)->count(),
        ];

        $this->chartData = $this->getChartData($branchId);
    }

    public function getRecentLoansProperty()
    {
        return Loan::with(['member', 'item.book'])
            ->where('branch_id', $this->getBranchId())
            ->latest('loan_date')
            ->limit(10)
            ->get();
    }

    public function getOverdueLoansProperty()
    {
        return Loan::with(['member', 'item.book'])
            ->where('branch_id', $this->getBranchId())
            ->where('is_returned', false)
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }

    protected function getChartData($branchId): array
    {
        $dailyLabels = [];
        $dailyLoans = [];
        $dailyReturns = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyLabels[] = $date->format('d M');
            $dailyLoans[] = Loan::where('branch_id', $branchId)->whereDate('loan_date', $date)->count();
            $dailyReturns[] = Loan::where('branch_id', $branchId)->whereDate('return_date', $date)->count();
        }

        $monthlyLabels = [];
        $monthlyLoans = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyLabels[] = Carbon::create(null, $i, 1)->format('M');
            $monthlyLoans[] = Loan::where('branch_id', $branchId)
                ->whereYear('loan_date', now()->year)
                ->whereMonth('loan_date', $i)
                ->count();
        }

        return [
            'daily' => [
                'labels' => $dailyLabels,
                'loans' => $dailyLoans,
                'returns' => $dailyReturns,
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'totals' => $monthlyLoans,
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
