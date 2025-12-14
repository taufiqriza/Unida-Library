<?php

namespace App\Livewire\Opac\Plagiarism;

use App\Models\PlagiarismCheck;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PlagiarismCertificate extends Component
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

        if (!$check->isCompleted() || !$check->hasCertificate()) {
            session()->flash('error', 'Sertifikat belum tersedia.');
            return redirect()->route('opac.member.plagiarism.show', $check);
        }

        $this->check = $check;
    }

    public function render()
    {
        return view('livewire.opac.plagiarism.plagiarism-certificate')
            ->layout('components.opac.layout', ['title' => 'Sertifikat Plagiasi']);
    }
}
