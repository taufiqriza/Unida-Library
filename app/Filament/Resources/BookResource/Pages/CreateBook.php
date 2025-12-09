<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use App\Models\Item;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function afterCreate(): void
    {
        $qty = $this->data['item_qty'] ?? 0;
        
        if ($qty > 0) {
            $lastItem = Item::orderBy('id', 'desc')->first();
            $lastNumber = $lastItem ? intval(substr($lastItem->barcode, -6)) : 0;
            
            for ($i = 0; $i < $qty; $i++) {
                $lastNumber++;
                $barcode = 'B' . str_pad($lastNumber, 6, '0', STR_PAD_LEFT);
                
                Item::create([
                    'book_id' => $this->record->id,
                    'branch_id' => $this->record->branch_id ?? 1,
                    'barcode' => $barcode,
                    'call_number' => $this->record->call_number,
                    'collection_type_id' => 1,
                    'location_id' => 1,
                    'item_status_id' => 1,
                    'user_id' => auth()->id(),
                ]);
            }
        }
    }
}
