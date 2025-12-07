<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Branch;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $branchId = $user->getCurrentBranchId();

        // Base queries
        $booksQuery = Book::query();
        $itemsQuery = Item::query();
        $membersQuery = Member::query();
        $loansQuery = Loan::where('is_returned', false);

        // Filter by branch for non-super-admin or when branch selected
        if (!$isSuperAdmin || $branchId) {
            $booksQuery->where('branch_id', $branchId ?? $user->branch_id);
            $itemsQuery->where('branch_id', $branchId ?? $user->branch_id);
            $membersQuery->where('branch_id', $branchId ?? $user->branch_id);
            $loansQuery->where('branch_id', $branchId ?? $user->branch_id);
        }

        $stats = [
            Stat::make('Total Koleksi', $booksQuery->count())
                ->description('Judul buku')
                ->icon('heroicon-o-book-open')
                ->color('primary'),
            Stat::make('Total Eksemplar', $itemsQuery->count())
                ->description('Item/copy')
                ->icon('heroicon-o-document-duplicate')
                ->color('success'),
            Stat::make('Total Anggota', $membersQuery->count())
                ->description('Member aktif')
                ->icon('heroicon-o-user-group')
                ->color('warning'),
            Stat::make('Sedang Dipinjam', $loansQuery->count())
                ->description('Belum dikembalikan')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('danger'),
        ];

        // Add branch & user stats for super admin
        if ($isSuperAdmin && !$branchId) {
            array_unshift($stats,
                Stat::make('Total Cabang', Branch::where('is_active', true)->count())
                    ->description('Cabang aktif')
                    ->icon('heroicon-o-building-library')
                    ->color('gray'),
            );
            $stats[] = Stat::make('Total Pengguna', User::where('is_active', true)->count())
                ->description('Admin & Pustakawan')
                ->icon('heroicon-o-users')
                ->color('info');
        }

        return $stats;
    }
}
