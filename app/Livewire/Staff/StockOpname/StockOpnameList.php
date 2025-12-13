<?php

namespace App\Livewire\Staff\StockOpname;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class StockOpnameList extends Component
{
    use WithPagination;

    public ?StockOpname $activeOpname = null;
    public string $barcode = '';
    public array $recentScans = [];
    public bool $showScanner = false;

    protected function getBranchId()
    {
        return auth()->user()->branch_id ?? session('staff_branch_id') ?? 1;
    }

    public function mount()
    {
        // Check for active stock opname
        $this->activeOpname = StockOpname::withoutGlobalScopes()
            ->where('branch_id', $this->getBranchId())
            ->where('status', 'in_progress')
            ->first();
    }

    public function openScanner(StockOpname $opname)
    {
        $this->activeOpname = $opname;
        $this->showScanner = true;
        $this->recentScans = [];
    }

    public function closeScanner()
    {
        $this->showScanner = false;
        $this->barcode = '';
    }

    public function scan()
    {
        if (empty(trim($this->barcode)) || !$this->activeOpname) {
            return;
        }

        $barcode = trim($this->barcode);
        
        // Find item by barcode
        $item = Item::withoutGlobalScopes()
            ->where('barcode', $barcode)
            ->where('branch_id', $this->activeOpname->branch_id)
            ->first();

        if (!$item) {
            $this->dispatch('notify', type: 'error', message: 'Barcode tidak ditemukan: ' . $barcode);
            $this->barcode = '';
            return;
        }

        // Find or create stock opname item
        $opnameItem = StockOpnameItem::where('stock_opname_id', $this->activeOpname->id)
            ->where('item_id', $item->id)
            ->first();

        if (!$opnameItem) {
            $opnameItem = StockOpnameItem::create([
                'stock_opname_id' => $this->activeOpname->id,
                'item_id' => $item->id,
                'status' => 'found',
                'checked_by' => auth()->id(),
                'checked_at' => now(),
            ]);
        } else {
            if ($opnameItem->status === 'found') {
                $this->dispatch('notify', type: 'warning', message: 'Item sudah di-scan sebelumnya');
                $this->barcode = '';
                return;
            }
            
            $opnameItem->update([
                'status' => 'found',
                'checked_by' => auth()->id(),
                'checked_at' => now(),
            ]);
        }

        // Update counts
        $this->activeOpname->updateCounts();
        $this->activeOpname->refresh();

        // Add to recent scans
        array_unshift($this->recentScans, [
            'title' => $item->book?->title ?? 'Unknown',
            'barcode' => $barcode,
            'time' => now()->format('H:i'),
        ]);
        $this->recentScans = array_slice($this->recentScans, 0, 5);

        $this->dispatch('notify', type: 'success', message: '✓ ' . ($item->book?->title ?? 'Item ditemukan'));
        $this->barcode = '';
    }

    public function getStatsProperty()
    {
        if (!$this->activeOpname) {
            return ['pending' => 0, 'found' => 0, 'missing' => 0, 'total' => 0];
        }

        return [
            'pending' => $this->activeOpname->total_items - $this->activeOpname->found_items - $this->activeOpname->missing_items,
            'found' => $this->activeOpname->found_items,
            'missing' => $this->activeOpname->missing_items,
            'total' => $this->activeOpname->total_items,
        ];
    }

    public function render()
    {
        $branchId = $this->getBranchId();
        
        $opnames = StockOpname::withoutGlobalScopes()
            ->where('branch_id', $branchId)
            ->latest()
            ->paginate(10);

        $stats = [
            'active' => StockOpname::withoutGlobalScopes()->where('branch_id', $branchId)->where('status', 'in_progress')->count(),
            'completed' => StockOpname::withoutGlobalScopes()->where('branch_id', $branchId)->where('status', 'completed')->count(),
            'total_items' => Item::withoutGlobalScopes()->where('branch_id', $branchId)->count(),
        ];

        return view('livewire.staff.stock-opname.stock-opname-list', [
            'opnames' => $opnames,
            'pageStats' => $stats,
        ])->extends('staff.layouts.app')->section('content');
    }
}
