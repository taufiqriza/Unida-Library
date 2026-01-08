<?php

namespace App\Livewire\Admin;

use App\Models\ShortUrl;
use Livewire\Component;

class ShortUrlGenerator extends Component
{
    public $originalUrl = '';
    public $title = '';
    public $customCode = '';
    public $generatedUrl = '';
    public $showModal = false;

    protected $rules = [
        'originalUrl' => 'required|url|max:2000',
        'title' => 'nullable|string|max:255',
        'customCode' => 'nullable|string|max:10|alpha_num'
    ];

    public function openModal()
    {
        $this->showModal = true;
        $this->reset(['originalUrl', 'title', 'customCode', 'generatedUrl']);
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function generate()
    {
        $this->validate();

        // Check if custom code already exists
        if ($this->customCode && ShortUrl::where('code', $this->customCode)->exists()) {
            $this->addError('customCode', 'Kode sudah digunakan, pilih kode lain.');
            return;
        }

        $code = $this->customCode ?: ShortUrl::generateUniqueCode();

        $shortUrl = ShortUrl::create([
            'code' => $code,
            'original_url' => $this->originalUrl,
            'title' => $this->title,
            'user_id' => auth()->id()
        ]);

        $this->generatedUrl = url('/s/' . $shortUrl->code);
        
        $this->dispatch('url-generated', [
            'url' => $this->generatedUrl,
            'code' => $shortUrl->code
        ]);
    }

    public function copyToClipboard()
    {
        $this->dispatch('copy-to-clipboard', ['text' => $this->generatedUrl]);
    }

    public function render()
    {
        return view('livewire.admin.short-url-generator');
    }
}
