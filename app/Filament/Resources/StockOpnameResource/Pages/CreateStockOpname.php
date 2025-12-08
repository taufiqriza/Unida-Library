<?php

namespace App\Filament\Resources\StockOpnameResource\Pages;

use App\Filament\Resources\StockOpnameResource;
use App\Models\Branch;
use Filament\Resources\Pages\CreateRecord;

class CreateStockOpname extends CreateRecord
{
    protected static string $resource = StockOpnameResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['code'] = (new \App\Models\StockOpname)->generateCode();
        
        // Set branch_id from current user's branch or session
        if (empty($data['branch_id'])) {
            $data['branch_id'] = auth('web')->user()->getCurrentBranchId() 
                ?? Branch::where('is_main', true)->first()?->id 
                ?? 1;
        }
        
        return $data;
    }
}
