<?php

namespace App\Livewire\Opac\Member;

use App\Models\ClearanceLetter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $member;
    public $loans;
    public $history;
    public $fines;
    public $submissions;
    public $clearanceLetters;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->member = Auth::guard('member')->user();
        $this->loans = $this->member->loans()->with('item.book')->where('is_returned', false)->get();
        $this->history = $this->member->loans()->with('item.book')->where('is_returned', true)->latest()->take(10)->get();
        $this->fines = $this->member->fines()->where('is_paid', false)->get();
        $this->submissions = $this->member->thesisSubmissions()->with('department')->latest()->get();
        $this->clearanceLetters = ClearanceLetter::where('member_id', $this->member->id)
            ->with('thesisSubmission')
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.opac.member.dashboard')
            ->layout('components.opac.layout', ['title' => 'Dashboard Member']);
    }
}
