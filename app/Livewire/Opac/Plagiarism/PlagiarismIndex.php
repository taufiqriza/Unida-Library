<?php

namespace App\Livewire\Opac\Plagiarism;

use App\Models\PlagiarismCheck;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PlagiarismIndex extends Component
{
    use WithPagination;

    public $member;

    public function mount()
    {
        $this->member = Auth::guard('member')->user();
    }

    public function render()
    {
        $checks = PlagiarismCheck::where('member_id', $this->member->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.opac.plagiarism.plagiarism-index', [
            'checks' => $checks,
        ])->layout('components.opac.layout', ['title' => 'Cek Plagiasi']);
    }
}
