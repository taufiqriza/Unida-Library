<?php

namespace App\Livewire\Opac;

use App\Models\Book;
use App\Models\Reservation;
use App\Services\Circulation\ReservationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CatalogShow extends Component
{
    public $book;
    public $relatedBooks;

    public function mount($id)
    {
        $this->book = Book::withoutGlobalScopes()
            ->with(['publisher', 'items' => fn($q) => $q->withoutGlobalScopes()->with('branch'), 'authors', 'subjects'])
            ->findOrFail($id);

        $this->relatedBooks = collect();
        if ($this->book->subjects->isNotEmpty()) {
            $this->relatedBooks = Book::withoutGlobalScopes()
                ->with('authors')
                ->whereHas('subjects', fn($q) => $q->whereIn('subjects.id', $this->book->subjects->pluck('id')))
                ->where('id', '!=', $this->book->id)
                ->take(4)
                ->get();
        }
    }

    public function reserve()
    {
        $member = Auth::guard('member')->user();
        if (!$member) {
            return redirect()->route('login');
        }

        try {
            $result = app(ReservationService::class)->reserve($member, $this->book);
            
            \Log::info('Reservation result', ['result' => $result, 'member' => $member->id, 'book' => $this->book->id]);
            
            if ($result['success']) {
                session()->flash('success', $result['message']);
                return redirect()->route('opac.member.loans');
            } else {
                $this->dispatch('showError', message: $result['message'], title: 'Reservasi Gagal');
            }
        } catch (\Exception $e) {
            \Log::error('Reservation error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->dispatch('showError', message: $e->getMessage(), title: 'Terjadi Kesalahan');
        }
    }

    public function getCanReserveProperty(): bool
    {
        $member = Auth::guard('member')->user();
        if (!$member) return false;
        
        // Semua eksemplar harus tidak tersedia
        if ($this->book->items->where('status', 'available')->count() > 0) return false;
        
        // Belum punya reservasi aktif untuk buku ini
        return !Reservation::where('member_id', $member->id)
            ->where('book_id', $this->book->id)
            ->whereIn('status', ['pending', 'ready'])
            ->exists();
    }

    public function getExistingReservationProperty()
    {
        $member = Auth::guard('member')->user();
        if (!$member) return null;
        
        return Reservation::where('member_id', $member->id)
            ->where('book_id', $this->book->id)
            ->whereIn('status', ['pending', 'ready'])
            ->first();
    }

    public function render()
    {
        return view('livewire.opac.catalog-show')
            ->layout('components.opac.layout', ['title' => $this->book->title]);
    }
}
