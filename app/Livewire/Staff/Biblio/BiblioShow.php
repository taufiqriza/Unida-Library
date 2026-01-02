<?php

namespace App\Livewire\Staff\Biblio;

use App\Models\Book;
use App\Models\Item;
use App\Models\Location;
use App\Models\ItemStatus;
use App\Models\CollectionType;
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

        $branchId = $this->book->branch_id;
        $locationId = Location::where('branch_id', $branchId)->value('id');
        $statusId = ItemStatus::where('name', 'Tersedia')->value('id') ?? ItemStatus::value('id');
        
        $today = now()->format('ymd');
        $lastItem = Item::where('inventory_code', 'like', "INV-{$branchId}-{$today}-%")
            ->orderByDesc('inventory_code')
            ->first();
        $lastNum = $lastItem ? (int) substr($lastItem->inventory_code, -4) : 0;

        for ($i = 0; $i < $this->addQty; $i++) {
            $lastNum++;
            Item::create([
                'book_id' => $this->book->id,
                'branch_id' => $branchId,
                'barcode' => 'B' . $today . str_pad($lastNum, 4, '0', STR_PAD_LEFT),
                'inventory_code' => "INV-{$branchId}-{$today}-" . str_pad($lastNum, 4, '0', STR_PAD_LEFT),
                'call_number' => $this->book->call_number,
                'collection_type_id' => CollectionType::value('id'),
                'location_id' => $locationId,
                'item_status_id' => $statusId,
                'source' => 'manual',
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
