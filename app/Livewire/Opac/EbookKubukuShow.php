<?php

namespace App\Livewire\Opac;

use App\Services\KubukuService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EbookKubukuShow extends Component
{
    public string $id;
    public ?array $ebook = null;
    public bool $loading = true;
    public ?string $error = null;
    public ?string $readUrl = null;
    public bool $showReadModal = false;

    public function mount(string $id)
    {
        $this->id = $id;
        $this->loadEbook();
    }

    protected function loadEbook(): void
    {
        $this->loading = true;
        
        try {
            $kubuku = app(KubukuService::class);
            
            if (!$kubuku->isEnabled()) {
                $this->error = 'KUBUKU service is not available';
                $this->loading = false;
                return;
            }

            $this->ebook = $kubuku->getById($this->id);
            
            if (!$this->ebook) {
                $this->error = 'E-Book not found';
            }
        } catch (\Exception $e) {
            $this->error = 'Failed to load e-book details';
            \Log::error('KUBUKU ebook load failed', [
                'id' => $this->id,
                'error' => $e->getMessage(),
            ]);
        }
        
        $this->loading = false;
    }

    public function openReader(): void
    {
        // Check if user is logged in
        if (!Auth::guard('member')->check()) {
            session()->flash('message', __('opac.kubuku.login_required'));
            $this->redirect(route('login', ['redirect' => url()->current()]));
            return;
        }

        $this->showReadModal = true;
    }

    public function confirmRead(): void
    {
        $member = Auth::guard('member')->user();
        
        if (!$member) {
            return;
        }

        try {
            $kubuku = app(KubukuService::class);
            
            $this->readUrl = $kubuku->getReadUrl(
                $this->id,
                $member->email,
                $member->name
            );

            if ($this->readUrl) {
                $this->dispatch('open-url', url: $this->readUrl);
            } else {
                session()->flash('error', __('opac.kubuku.read_error'));
            }
        } catch (\Exception $e) {
            session()->flash('error', __('opac.kubuku.read_error'));
            \Log::error('KUBUKU read URL failed', [
                'id' => $this->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->showReadModal = false;
    }

    public function cancelRead(): void
    {
        $this->showReadModal = false;
    }

    public function render()
    {
        return view('livewire.opac.ebook-kubuku-show')
            ->layout('layouts.opac', [
                'title' => $this->ebook['title'] ?? 'E-Book KUBUKU',
            ]);
    }
}
