<?php

namespace App\Filament\Pages;

use App\Models\Book;
use App\Models\Branch;
use App\Models\Ebook;
use App\Models\Fine;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Ringkasan';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.reports';

    public string $period = 'month';

    public function getStartDate(): Carbon
    {
        return match($this->period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }

    public function getCurrentBranch(): ?Branch
    {
        $branchId = auth()->user()->getCurrentBranchId();
        return $branchId ? Branch::find($branchId) : null;
    }

    public function getCollectionStats(): array
    {
        return [
            'books' => Book::count(),
            'items' => Item::count(),
            'ebooks' => Ebook::count(),
        ];
    }

    public function getMemberStats(): array
    {
        $start = $this->getStartDate();
        return [
            'total' => Member::count(),
            'active' => Member::where('is_active', true)->where('expire_date', '>=', now())->count(),
            'new' => Member::where('created_at', '>=', $start)->count(),
            'expired' => Member::where('expire_date', '<', now())->count(),
        ];
    }

    public function getLoanStats(): array
    {
        $start = $this->getStartDate();
        return [
            'total' => Loan::where('loan_date', '>=', $start)->count(),
            'returned' => Loan::where('return_date', '>=', $start)->count(),
            'active' => Loan::where('is_returned', false)->count(),
            'overdue' => Loan::where('is_returned', false)->where('due_date', '<', now())->count(),
        ];
    }

    public function getFineStats(): array
    {
        $start = $this->getStartDate();
        return [
            'total' => Fine::where('created_at', '>=', $start)->sum('amount'),
            'paid' => Fine::where('created_at', '>=', $start)->sum('paid_amount'),
            'unpaid' => Fine::where('is_paid', false)->sum('amount') - Fine::where('is_paid', false)->sum('paid_amount'),
        ];
    }
}
