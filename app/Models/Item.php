<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'book_id', 'branch_id', 'barcode', 'call_number', 'collection_type_id',
        'location_id', 'item_status_id', 'inventory_code', 'received_date',
        'source', 'supplier_id', 'order_no', 'order_date', 'invoice',
        'invoice_date', 'price', 'site', 'user_id'
    ];

    protected $casts = [
        'received_date' => 'date',
        'order_date' => 'date',
        'invoice_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function book(): BelongsTo { return $this->belongsTo(Book::class); }
    public function collectionType(): BelongsTo { return $this->belongsTo(CollectionType::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function itemStatus(): BelongsTo { return $this->belongsTo(ItemStatus::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function loans(): HasMany { return $this->hasMany(Loan::class); }

    public function isAvailable(): bool
    {
        return !$this->itemStatus?->no_loan && !$this->loans()->where('is_returned', false)->exists();
    }
}
