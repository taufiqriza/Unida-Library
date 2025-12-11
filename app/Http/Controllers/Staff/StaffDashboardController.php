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
    public function index()
    {
        $user = auth()->user();
        $branchId = $user->branch_id ?? session('staff_branch_id') ?? 1;

        $stats = [
            'loans_today' => Loan::where('branch_id', $branchId)->whereDate('loan_date', today())->count(),
            'returns_today' => Loan::where('branch_id', $branchId)->whereDate('return_date', today())->count(),
            'overdue' => Loan::where('branch_id', $branchId)->where('is_returned', false)->where('due_date', '<', now())->count(),
            'unpaid_fines' => Fine::whereHas('loan', fn($q) => $q->where('branch_id', $branchId))->where('is_paid', false)->sum('amount'),
            'total_books' => Book::where('branch_id', $branchId)->count(),
            'total_items' => Item::where('branch_id', $branchId)->count(),
            'total_members' => Member::where('branch_id', $branchId)->count(),
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

        return view('staff.dashboard.index', compact('stats', 'recentLoans', 'overdueLoans'));
    }
}
