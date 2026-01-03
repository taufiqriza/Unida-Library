<?php

namespace App\Livewire\Opac\Member;

use App\Models\ClearanceLetter;
use App\Models\PlagiarismCheck;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Dashboard extends Component
{
    use WithFileUploads;

    public $member;
    public $loans;
    public $history;
    public $fines;
    public $submissions;
    public $clearanceLetters;
    public $plagiarismCertificates;
    public $photo;
    
    // Digital Card
    public bool $showDigitalCard = false;

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
        $this->plagiarismCertificates = PlagiarismCheck::where('member_id', $this->member->id)
            ->whereNotNull('certificate_number')
            ->latest()
            ->get();
    }

    public function updatedPhoto()
    {
        $this->validate(['photo' => 'image|max:2048']);
        
        if ($this->member->photo) {
            Storage::disk('public')->delete($this->member->photo);
        }
        
        $path = $this->photo->store('members/photos', 'public');
        $this->member->update(['photo' => $path]);
        $this->photo = null;
        
        $this->dispatch('notify', type: 'success', message: 'Foto profil berhasil diperbarui');
    }

    public function getQrCodeProperty(): string
    {
        $token = encrypt([
            'id' => $this->member->id,
            'member_id' => $this->member->member_id,
            'exp' => now()->addDay()->timestamp
        ]);
        
        return base64_encode(QrCode::format('svg')->size(200)->margin(1)->generate($token));
    }

    public function render()
    {
        return view('livewire.opac.member.dashboard')
            ->layout('components.opac.layout', ['title' => 'Dashboard Member']);
    }
}
