<?php

namespace App\Livewire;

use App\Models\Branch;
use Livewire\Component;

class BranchSwitcher extends Component
{
    public ?int $currentBranchId = null;

    public function mount(): void
    {
        $this->currentBranchId = session('current_branch_id');
    }

    public function switchBranch(?int $branchId): void
    {
        if ($branchId) {
            session(['current_branch_id' => $branchId]);
        } else {
            session()->forget('current_branch_id');
        }
        $this->currentBranchId = $branchId;
        $this->redirect(request()->header('Referer', '/admin'));
    }

    public function render()
    {
        return view('livewire.branch-switcher', [
            'branches' => Branch::where('is_active', true)->get(),
            'currentBranch' => $this->currentBranchId ? Branch::find($this->currentBranchId) : null,
        ]);
    }
}
