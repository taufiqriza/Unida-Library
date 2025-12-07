<?php

namespace App\Filament\Pages;

use App\Models\Book;
use App\Models\Branch;
use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\Fine;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use App\Models\News;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;
    protected static string $view = 'filament.pages.dashboard';

    public function getHeading(): string
    {
        return '';
    }

    public function getBranch(): ?Branch
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            $branchId = session('current_branch_id');
            return $branchId ? Branch::find($branchId) : null;
        }
        return $user->branch;
    }

    public function getGreeting(): string
    {
        $hour = now()->hour;
        if ($hour < 12) return 'Selamat Pagi';
        if ($hour < 15) return 'Selamat Siang';
        if ($hour < 18) return 'Selamat Sore';
        return 'Selamat Malam';
    }

    public function getMainStats(): array
    {
        $user = auth()->user();
        $branchId = $user->getCurrentBranchId();
        $filterBranch = !$user->isSuperAdmin() || $branchId;

        return [
            'books' => $filterBranch ? Book::where('branch_id', $branchId ?? $user->branch_id)->count() : Book::count(),
            'items' => $filterBranch ? Item::where('branch_id', $branchId ?? $user->branch_id)->count() : Item::count(),
            'members' => $filterBranch ? Member::where('branch_id', $branchId ?? $user->branch_id)->count() : Member::count(),
            'active_loans' => $filterBranch 
                ? Loan::where('branch_id', $branchId ?? $user->branch_id)->where('is_returned', false)->count() 
                : Loan::where('is_returned', false)->count(),
        ];
    }

    public function getAlertStats(): array
    {
        $user = auth()->user();
        $branchId = $user->getCurrentBranchId();
        $filterBranch = !$user->isSuperAdmin() || $branchId;

        $overdueQuery = Loan::where('is_returned', false)->where('due_date', '<', now());
        $expiredQuery = Member::where('expire_date', '<', now());
        $unpaidQuery = Fine::where('is_paid', false);

        if ($filterBranch) {
            $overdueQuery->where('branch_id', $branchId ?? $user->branch_id);
            $expiredQuery->where('branch_id', $branchId ?? $user->branch_id);
            $unpaidQuery->whereHas('loan', fn($q) => $q->where('branch_id', $branchId ?? $user->branch_id));
        }

        return [
            'overdue' => $overdueQuery->count(),
            'expired_members' => $expiredQuery->count(),
            'unpaid_fines' => $unpaidQuery->count(),
            'unpaid_amount' => $unpaidQuery->sum('amount') - $unpaidQuery->sum('paid_amount'),
        ];
    }

    public function getDigitalStats(): array
    {
        return [
            'ebooks' => Ebook::count(),
            'ethesis' => Ethesis::count(),
            'news' => News::where('status', 'published')->count(),
        ];
    }

    public function getLoanChartData(): array
    {
        $user = auth()->user();
        $branchId = $user->getCurrentBranchId();
        $filterBranch = !$user->isSuperAdmin() || $branchId;

        $labels = [];
        $loans = [];
        $returns = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M');

            $loanQuery = Loan::whereDate('loan_date', $date);
            $returnQuery = Loan::whereDate('return_date', $date);

            if ($filterBranch) {
                $loanQuery->where('branch_id', $branchId ?? $user->branch_id);
                $returnQuery->where('branch_id', $branchId ?? $user->branch_id);
            }

            $loans[] = $loanQuery->count();
            $returns[] = $returnQuery->count();
        }

        return compact('labels', 'loans', 'returns');
    }

    public function getRecentLoans(): array
    {
        $user = auth()->user();
        $branchId = $user->getCurrentBranchId();

        $query = Loan::with(['member', 'item.book'])->latest('loan_date');

        if (!$user->isSuperAdmin() || $branchId) {
            $query->where('branch_id', $branchId ?? $user->branch_id);
        }

        return $query->limit(5)->get()->toArray();
    }

    public function getTopBooks(): array
    {
        return Book::withCount('items')
            ->orderByDesc('items_count')
            ->limit(5)
            ->get(['id', 'title'])
            ->toArray();
    }
}
