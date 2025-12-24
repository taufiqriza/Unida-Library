<?php

namespace App\Livewire\Opac\Member;

use App\Models\ClearanceLetter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClearanceLetterPrint extends Component
{
    public ClearanceLetter $letter;

    public function mount(ClearanceLetter $letter)
    {
        // Ensure the letter belongs to the current member
        $member = Auth::guard('member')->user();
        
        if ($letter->member_id !== $member->id) {
            abort(403, 'Akses ditolak');
        }
        
        if ($letter->status !== 'approved') {
            abort(404, 'Surat belum disetujui');
        }
        
        $this->letter = $letter->load(['member', 'thesisSubmission', 'approver']);
    }

    public function render()
    {
        return view('livewire.opac.member.clearance-letter-print')
            ->layout('components.opac.print-layout', [
                'title' => 'Surat Bebas Pustaka - ' . $this->letter->letter_number
            ]);
    }
}
