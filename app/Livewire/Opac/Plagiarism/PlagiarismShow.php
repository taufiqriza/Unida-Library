<?php

namespace App\Livewire\Opac\Plagiarism;

use App\Models\PlagiarismCheck;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PlagiarismShow extends Component
{
    public PlagiarismCheck $check;
    public $member;

    public function mount(PlagiarismCheck $check)
    {
        $this->member = Auth::guard('member')->user();

        // Authorization: only owner can view
        if ($check->member_id !== $this->member->id) {
            abort(403, 'Akses ditolak');
        }

        $this->check = $check;
    }

    public function refreshStatus()
    {
        $this->check->refresh();
    }

    public function render()
    {
        return view('livewire.opac.plagiarism.plagiarism-show')
            ->layout('components.opac.layout', ['title' => 'Detail Cek Plagiasi']);
    }
}
