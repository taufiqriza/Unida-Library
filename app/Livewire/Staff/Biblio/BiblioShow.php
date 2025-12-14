<?php

namespace App\Livewire\Staff\Biblio;

use App\Models\Book;
use App\Models\Item;
use Livewire\Component;

class BiblioShow extends Component
{
    public Book $book;
    public int $addQty = 1;
    public bool $showAddModal = false;

    public function mount($book)
    {
        $this->book = Book::with(['publisher', 'mediaType', 'authors', 'subjects', 'items.itemStatus', 'items.location'])
            ->findOrFail($book);
    }

    public function openAddModal()
    {
        $this->addQty = 1;
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function addItems()
    {
        $this->validate([
            'addQty' => 'required|integer|min:1|max:50',
        ]);

        $lastItem = Item::orderBy('id', 'desc')->first();
        $lastNumber = $lastItem ? intval(substr($lastItem->barcode, -6)) : 0;

        for ($i = 0; $i < $this->addQty; $i++) {
            $lastNumber++;
            Item::create([
                'book_id' => $this->book->id,
                'branch_id' => $this->book->branch_id,
                'barcode' => 'B' . str_pad($lastNumber, 6, '0', STR_PAD_LEFT),
                'call_number' => $this->book->call_number,
                'collection_type_id' => 1,
                'location_id' => 1,
                'item_status_id' => 1,
                'user_id' => auth()->id(),
            ]);
        }

        $this->book->load('items.itemStatus', 'items.location');
        $this->closeAddModal();
        
        $this->dispatch('notify', type: 'success', message: "{$this->addQty} eksemplar berhasil ditambahkan");
    }

    public function render()
    {
        return view('livewire.staff.biblio.biblio-show')
            ->extends('staff.layouts.app')
            ->section('content');
    }
}
