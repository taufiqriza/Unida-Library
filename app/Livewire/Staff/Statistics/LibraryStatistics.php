<?php

namespace App\Livewire\Staff\Statistics;

use App\Models\Book;
use App\Models\Branch;
use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\Fine;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LibraryStatistics extends Component
{
    public ?int $selectedBranch = null;
    public string $viewMode = 'all'; // all, branch
    public array $stats = [];
    public array $branchStats = [];
    public array $chartData = [];
    public array $topCategories = [];
    public array $monthlyTrend = [];

    public function mount()
    {
        $user = auth()->user();
        
        // Default to user's branch if not super_admin
        if (!in_array($user->role, ['super_admin', 'admin'])) {
            $this->selectedBranch = $user->branch_id;
            $this->viewMode = 'branch';
        }
        
        $this->loadStatistics();
    }

    public function updatedSelectedBranch()
    {
        $this->viewMode = $this->selectedBranch ? 'branch' : 'all';
        $this->loadStatistics();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        if ($mode === 'all') {
            $this->selectedBranch = null;
        }
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $branchId = $this->selectedBranch;

        // Main Stats
        $this->stats = [
            // Collection Stats
            'total_titles' => $this->queryWithBranch(Book::query(), $branchId)->count(),
            'total_items' => $this->queryWithBranch(Item::query(), $branchId)->count(),
            'available_items' => $this->queryWithBranch(Item::query(), $branchId)
                ->whereDoesntHave('loans', fn($q) => $q->where('is_returned', false))->count(),
            'on_loan_items' => $this->queryWithBranch(Item::query(), $branchId)
                ->whereHas('loans', fn($q) => $q->where('is_returned', false))->count(),
            
            // Digital Collection (no branch filter - shared across all)
            'total_ebooks' => Ebook::where('is_active', true)->count(),
            'total_ethesis' => Ethesis::where('is_public', true)->count(),
            
            // Member Stats
            'total_members' => $this->queryWithBranch(Member::query(), $branchId)->count(),
            'active_members' => $this->queryWithBranch(Member::query(), $branchId)
                ->where('expire_date', '>=', now())->count(),
            'new_members_month' => $this->queryWithBranch(Member::query(), $branchId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            
            // Circulation Stats
            'loans_today' => $this->queryWithBranch(Loan::query(), $branchId)
                ->whereDate('loan_date', today())->count(),
            'loans_this_month' => $this->queryWithBranch(Loan::query(), $branchId)
                ->whereMonth('loan_date', now()->month)
                ->whereYear('loan_date', now()->year)->count(),
            'loans_this_year' => $this->queryWithBranch(Loan::query(), $branchId)
                ->whereYear('loan_date', now()->year)->count(),
            'active_loans' => $this->queryWithBranch(Loan::query(), $branchId)
                ->where('is_returned', false)->count(),
            'overdue_loans' => $this->queryWithBranch(Loan::query(), $branchId)
                ->where('is_returned', false)
                ->where('due_date', '<', now())->count(),
            
            // Fine Stats (fines has branch_id directly)
            'total_fines' => $this->queryWithBranch(Fine::query(), $branchId)->sum('amount'),
            'unpaid_fines' => $this->queryWithBranch(Fine::query(), $branchId)
                ->where('is_paid', false)->sum('amount'),
            'paid_fines' => $this->queryWithBranch(Fine::query(), $branchId)
                ->where('is_paid', true)->sum('amount'),
        ];

        // Branch Comparison (only for all view)
        if ($this->viewMode === 'all') {
            $this->branchStats = Branch::with(['books', 'items', 'members'])
                ->get()
                ->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'code' => $branch->code,
                        'titles' => $branch->books->count(),
                        'items' => $branch->items->count(),
                        'members' => $branch->members->count(),
                        'loans_month' => Loan::where('branch_id', $branch->id)
                            ->whereMonth('loan_date', now()->month)
                            ->whereYear('loan_date', now()->year)->count(),
                    ];
                })->toArray();
        }

        // Top Categories
        $this->topCategories = $this->queryWithBranch(Book::query(), $branchId)
            ->select('classification', DB::raw('count(*) as total'))
            ->whereNotNull('classification')
            ->groupBy('classification')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();

        // Monthly Trend (12 months)
        $this->monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $this->monthlyTrend[] = [
                'month' => $date->format('M Y'),
                'loans' => $this->queryWithBranch(Loan::query(), $branchId)
                    ->whereMonth('loan_date', $date->month)
                    ->whereYear('loan_date', $date->year)->count(),
                'returns' => $this->queryWithBranch(Loan::query(), $branchId)
                    ->whereMonth('return_date', $date->month)
                    ->whereYear('return_date', $date->year)->count(),
                'new_members' => $this->queryWithBranch(Member::query(), $branchId)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
            ];
        }

        // Chart Data
        $this->chartData = [
            'monthly' => [
                'labels' => array_column($this->monthlyTrend, 'month'),
                'loans' => array_column($this->monthlyTrend, 'loans'),
                'returns' => array_column($this->monthlyTrend, 'returns'),
                'members' => array_column($this->monthlyTrend, 'new_members'),
            ],
            'categories' => [
                'labels' => array_column($this->topCategories, 'classification'),
                'values' => array_column($this->topCategories, 'total'),
            ],
        ];
    }

    protected function queryWithBranch($query, ?int $branchId, string $column = 'branch_id')
    {
        if ($branchId) {
            return $query->where($column, $branchId);
        }
        return $query;
    }

    public function getBranchesProperty()
    {
        return Branch::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.staff.statistics.library-statistics', [
            'branches' => $this->branches,
        ])->extends('staff.layouts.app')->section('content');
    }
}
