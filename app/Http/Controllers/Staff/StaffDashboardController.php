<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Support\Carbon;

class StaffDashboardController extends Controller
{
    protected function getBranchId()
    {
        $user = auth()->user();
        return $user->branch_id ?? session('staff_branch_id') ?? 1;
    }

    public function index()
    {
        $branchId = $this->getBranchId();

        $stats = [
            'loans_today' => Loan::where('branch_id', $branchId)->whereDate('loan_date', today())->count(),
            'returns_today' => Loan::where('branch_id', $branchId)->whereDate('return_date', today())->count(),
            'overdue' => Loan::where('branch_id', $branchId)->where('is_returned', false)->where('due_date', '<', now())->count(),
            'unpaid_fines' => Fine::whereHas('loan', fn($q) => $q->where('branch_id', $branchId))->where('is_paid', false)->sum('amount'),
            'total_books' => Book::where('branch_id', $branchId)->count(),
            'total_items' => Item::where('branch_id', $branchId)->count(),
            'total_members' => Member::where('branch_id', $branchId)->count(),
            'active_loans' => Loan::where('branch_id', $branchId)->where('is_returned', false)->count(),
        ];

        $recentLoans = Loan::with(['member', 'item.book'])
            ->where('branch_id', $branchId)
            ->latest('loan_date')
            ->limit(10)
            ->get();

        $overdueLoans = Loan::with(['member', 'item.book'])
            ->where('branch_id', $branchId)
            ->where('is_returned', false)
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $chartData = $this->getChartData($branchId);

        return view('staff.dashboard.index', compact('stats', 'recentLoans', 'overdueLoans', 'chartData'));
    }

    protected function getChartData($branchId): array
    {
        // 7 Days Loan Chart
        $dailyLabels = [];
        $dailyLoans = [];
        $dailyReturns = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyLabels[] = $date->format('d M');
            $dailyLoans[] = Loan::where('branch_id', $branchId)->whereDate('loan_date', $date)->count();
            $dailyReturns[] = Loan::where('branch_id', $branchId)->whereDate('return_date', $date)->count();
        }

        // Monthly Loan Chart (current year)
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
}
