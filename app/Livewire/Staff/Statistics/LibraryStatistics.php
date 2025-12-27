<?php

namespace App\Livewire\Staff\Statistics;

use App\Models\Author;
use App\Models\Book;
use App\Models\Branch;
use App\Models\CollectionType;
use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\Fine;
use App\Models\Item;
use App\Models\Loan;
use App\Models\MediaType;
use App\Models\Member;
use App\Models\Publisher;
use App\Models\Subject;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LibraryStatistics extends Component
{
    public ?int $selectedBranch = null;
    public string $viewMode = 'all';
    public string $activeTab = 'overview'; // overview, collection, circulation, digital
    public array $stats = [];
    public array $branchStats = [];
    public array $chartData = [];
    public array $topCategories = [];
    public array $monthlyTrend = [];
    
    // Collection Classification Data
    public array $byMediaType = [];
    public array $byCollectionType = [];
    public array $byClassification = [];
    public array $byLanguage = [];
    public array $byPublisher = [];
    public array $byYear = [];
    public array $bySubject = [];
    public array $byAuthor = [];

    protected $queryString = ['selectedBranch', 'activeTab'];

    public function mount()
    {
        $user = auth()->user();
        if ($user->role !== 'super_admin') {
            $this->selectedBranch = $user->branch_id;
            $this->viewMode = 'branch';
        }
        $this->loadStatistics();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updatedSelectedBranch()
    {
        $this->viewMode = $this->selectedBranch ? 'branch' : 'all';
        Cache::forget('staff_statistics_' . ($this->selectedBranch ?? 'all'));
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $branchId = $this->selectedBranch;
        $cacheKey = 'staff_statistics_' . ($branchId ?? 'all');
        
        // Cache for 15 minutes (heavy queries)
        $data = Cache::remember($cacheKey, 900, function () use ($branchId) {
            return $this->fetchAllStatistics($branchId);
        });
        
        $this->stats = $data['stats'];
        $this->branchStats = $data['branchStats'];
        $this->topCategories = $data['topCategories'];
        $this->monthlyTrend = $data['monthlyTrend'];
        $this->chartData = $data['chartData'];
        $this->byMediaType = $data['byMediaType'];
        $this->byCollectionType = $data['byCollectionType'];
        $this->byClassification = $data['byClassification'];
        $this->byLanguage = $data['byLanguage'];
        $this->byPublisher = $data['byPublisher'];
        $this->byYear = $data['byYear'];
        $this->bySubject = $data['bySubject'];
        $this->byAuthor = $data['byAuthor'];
    }

    protected function fetchAllStatistics(?int $branchId): array
    {
        // Main Stats
        $stats = [
            'total_titles' => $this->queryWithBranch(Book::query(), $branchId)->count(),
            'total_items' => $this->queryWithBranch(Item::query(), $branchId)->count(),
            'available_items' => $this->queryWithBranch(Item::query(), $branchId)
                ->whereDoesntHave('loans', fn($q) => $q->where('is_returned', false))->count(),
            'on_loan_items' => $this->queryWithBranch(Item::query(), $branchId)
                ->whereHas('loans', fn($q) => $q->where('is_returned', false))->count(),
            'total_ebooks' => Ebook::where('is_active', true)->count(),
            'total_ethesis' => Ethesis::where('is_public', true)->count(),
            'total_members' => $this->queryWithBranch(Member::query(), $branchId)->count(),
            'active_members' => $this->queryWithBranch(Member::query(), $branchId)
                ->where('expire_date', '>=', now())->count(),
            'new_members_month' => $this->queryWithBranch(Member::query(), $branchId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
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
            'total_fines' => $this->queryWithBranch(Fine::query(), $branchId)->sum('amount'),
            'unpaid_fines' => $this->queryWithBranch(Fine::query(), $branchId)
                ->where('is_paid', false)->sum('amount'),
            'paid_fines' => $this->queryWithBranch(Fine::query(), $branchId)
                ->where('is_paid', true)->sum('amount'),
            'total_authors' => Author::whereHas('books', fn($q) => $branchId ? $q->where('branch_id', $branchId) : $q)->count(),
            'total_publishers' => Publisher::whereHas('books', fn($q) => $branchId ? $q->where('branch_id', $branchId) : $q)->count(),
            'total_subjects' => Subject::whereHas('books', fn($q) => $branchId ? $q->where('branch_id', $branchId) : $q)->count(),
        ];

        // Branch Stats - Always fetch for 'all' view (bypass global scope)
        $branchStats = [];
        if (!$branchId) {
            $branches = Branch::orderBy('name')->get();
            foreach ($branches as $branch) {
                $branchStats[] = [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'code' => $branch->code ?? substr($branch->name, 0, 3),
                    'titles' => Book::withoutGlobalScope('branch')->where('branch_id', $branch->id)->count(),
                    'items' => Item::withoutGlobalScope('branch')->where('branch_id', $branch->id)->count(),
                    'members' => Member::withoutGlobalScope('branch')->where('branch_id', $branch->id)->count(),
                    'loans_month' => Loan::withoutGlobalScope('branch')->where('branch_id', $branch->id)
                        ->whereMonth('loan_date', now()->month)
                        ->whereYear('loan_date', now()->year)->count(),
                ];
            }
        }

        // Top Categories (Classification)
        $topCategories = $this->queryWithBranch(Book::query(), $branchId)
            ->select('classification', DB::raw('count(*) as total'))
            ->whereNotNull('classification')
            ->where('classification', '!=', '')
            ->groupBy('classification')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();

        // Monthly Trend - Optimized: 3 queries instead of 36
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();
        
        $loansPerMonth = $this->queryWithBranch(Loan::query(), $branchId)
            ->whereBetween('loan_date', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(loan_date, '%Y-%m') as month_key, COUNT(*) as count")
            ->groupBy('month_key')
            ->pluck('count', 'month_key')
            ->toArray();
            
        $returnsPerMonth = $this->queryWithBranch(Loan::query(), $branchId)
            ->whereBetween('return_date', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(return_date, '%Y-%m') as month_key, COUNT(*) as count")
            ->groupBy('month_key')
            ->pluck('count', 'month_key')
            ->toArray();
            
        $membersPerMonth = $this->queryWithBranch(Member::query(), $branchId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as count")
            ->groupBy('month_key')
            ->pluck('count', 'month_key')
            ->toArray();

        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $monthlyTrend[] = [
                'month' => $date->format('M Y'),
                'short' => $date->format('M'),
                'loans' => $loansPerMonth[$key] ?? 0,
                'returns' => $returnsPerMonth[$key] ?? 0,
                'new_members' => $membersPerMonth[$key] ?? 0,
            ];
        }

        // Collection Classification Stats
        $byMediaType = MediaType::withCount(['books' => fn($q) => $branchId ? $q->where('branch_id', $branchId) : $q])
            ->get()
            ->filter(fn($m) => $m->books_count > 0)
            ->sortByDesc('books_count')
            ->values()
            ->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'count' => $m->books_count])
            ->toArray();

        $byCollectionType = CollectionType::withCount(['items' => fn($q) => $branchId ? $q->where('branch_id', $branchId) : $q])
            ->get()
            ->filter(fn($c) => $c->items_count > 0)
            ->sortByDesc('items_count')
            ->values()
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'count' => $c->items_count])
            ->toArray();

        $byClassification = $this->queryWithBranch(Book::query(), $branchId)
            ->select('classification', DB::raw('COUNT(*) as count'))
            ->whereNotNull('classification')
            ->where('classification', '!=', '')
            ->groupBy('classification')
            ->orderByDesc('count')
            ->limit(15)
            ->get()
            ->toArray();

        $byLanguage = $this->queryWithBranch(Book::query(), $branchId)
            ->select('language', DB::raw('COUNT(*) as count'))
            ->whereNotNull('language')
            ->where('language', '!=', '')
            ->groupBy('language')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();

        $byPublisher = Publisher::withCount(['books' => fn($q) => $branchId ? $q->where('branch_id', $branchId) : $q])
            ->get()
            ->filter(fn($p) => $p->books_count > 0)
            ->sortByDesc('books_count')
            ->take(10)
            ->values()
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'count' => $p->books_count])
            ->toArray();

        $byYear = $this->queryWithBranch(Book::query(), $branchId)
            ->select('publish_year', DB::raw('COUNT(*) as count'))
            ->whereNotNull('publish_year')
            ->where('publish_year', '!=', '')
            ->groupBy('publish_year')
            ->orderByDesc('publish_year')
            ->limit(15)
            ->get()
            ->toArray();

        $bySubject = Subject::withCount(['books' => fn($q) => $branchId ? $q->where('branch_id', $branchId) : $q])
            ->get()
            ->filter(fn($s) => $s->books_count > 0)
            ->sortByDesc('books_count')
            ->take(12)
            ->values()
            ->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'count' => $s->books_count])
            ->toArray();

        $byAuthor = Author::withCount(['books' => fn($q) => $branchId ? $q->where('branch_id', $branchId) : $q])
            ->get()
            ->filter(fn($a) => $a->books_count > 0)
            ->sortByDesc('books_count')
            ->take(12)
            ->values()
            ->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'count' => $a->books_count])
            ->toArray();

        return [
            'stats' => $stats,
            'branchStats' => $branchStats,
            'topCategories' => $topCategories,
            'monthlyTrend' => $monthlyTrend,
            'chartData' => [
                'monthly' => [
                    'labels' => array_column($monthlyTrend, 'short'),
                    'loans' => array_column($monthlyTrend, 'loans'),
                    'returns' => array_column($monthlyTrend, 'returns'),
                    'members' => array_column($monthlyTrend, 'new_members'),
                ],
                'categories' => [
                    'labels' => array_column($topCategories, 'classification'),
                    'values' => array_column($topCategories, 'total'),
                ],
            ],
            'byMediaType' => $byMediaType,
            'byCollectionType' => $byCollectionType,
            'byClassification' => $byClassification,
            'byLanguage' => $byLanguage,
            'byPublisher' => $byPublisher,
            'byYear' => $byYear,
            'bySubject' => $bySubject,
            'byAuthor' => $byAuthor,
        ];
    }

    protected function queryWithBranch($query, ?int $branchId, string $column = 'branch_id')
    {
        // Always bypass global scope for statistics to show accurate data
        $query = $query->withoutGlobalScope('branch');
        
        if ($branchId) {
            return $query->where($column, $branchId);
        }
        return $query;
    }

    public function getBranchesProperty()
    {
        return Branch::orderBy('name')->get(['id', 'name', 'code']);
    }

    public function render()
    {
        return view('livewire.staff.statistics.library-statistics', [
            'branches' => $this->branches,
        ])->extends('staff.layouts.app')->section('content');
    }
}
