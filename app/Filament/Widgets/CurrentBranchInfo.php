<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use Filament\Widgets\Widget;

class CurrentBranchInfo extends Widget
{
    protected static string $view = 'filament.widgets.current-branch-info';
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'full';

    public function getBranch(): ?Branch
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            $branchId = session('current_branch_id');
            return $branchId ? Branch::find($branchId) : null;
        }
        return $user->branch;
    }
}
