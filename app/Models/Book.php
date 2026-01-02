<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Book extends Model
{
    use BelongsToBranch, Searchable;

    protected $fillable = [
        'branch_id', 'user_id', 'title', 'sor', 'isbn', 'publisher_id', 'place_id',
        'publish_year', 'edition', 'collation', 'series_title', 'call_number',
        'classification', 'notes', 'image', 'media_type_id', 'content_type_id',
        'carrier_type_id', 'frequency_id', 'language', 'abstract', 'spec_detail_info',
        'is_opac_visible', 'opac_hide', 'promoted', 'labels', 'input_date'
    ];

    protected $casts = [
        'is_opac_visible' => 'boolean',
        'opac_hide' => 'boolean',
        'promoted' => 'boolean',
        'input_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($book) {
            // Delete related items
            $book->items()->each(function ($item) {
                // Check if item has active loans (not returned)
                if ($item->loans()->where('is_returned', false)->exists()) {
                    throw new \Exception("Tidak dapat menghapus buku karena ada peminjaman aktif.");
                }
                // Delete completed loans first
                $item->loans()->delete();
                $item->delete();
            });
            // Delete attachments
            $book->attachments()->delete();
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function publisher(): BelongsTo { return $this->belongsTo(Publisher::class); }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function mediaType(): BelongsTo { return $this->belongsTo(MediaType::class); }
    public function contentType(): BelongsTo { return $this->belongsTo(ContentType::class); }
    public function carrierType(): BelongsTo { return $this->belongsTo(CarrierType::class); }
    public function frequency(): BelongsTo { return $this->belongsTo(Frequency::class); }
    
    public function items(): HasMany { return $this->hasMany(Item::class); }
    public function attachments(): HasMany { return $this->hasMany(BookAttachment::class); }
    
    public function authors(): BelongsToMany { 
        return $this->belongsToMany(Author::class, 'book_author')->withPivot('level')->orderByPivot('level'); 
    }
    public function subjects(): BelongsToMany { 
        return $this->belongsToMany(Subject::class); 
    }

    public function getAuthorNamesAttribute(): string
    {
        return $this->authors()->get()->pluck('name')->implode('; ');
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        // Normalize path - add covers/ prefix if not present
        $path = str_starts_with($this->image, 'covers/') ? $this->image : 'covers/' . $this->image;
        return asset('storage/' . $path);
    }

    public function getFileUrlAttribute(): ?string
    {
        $attachment = $this->attachments()->first();
        return $attachment ? asset('storage/' . $attachment->file_path) : null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loans()
    {
        return $this->hasManyThrough(Loan::class, Item::class);
    }

    public function collectionType(): BelongsTo
    {
        return $this->belongsTo(CollectionType::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'call_number' => $this->call_number,
            'author' => $this->author_names,
            'publisher' => $this->publisher?->name,
            'year' => $this->publish_year,
            'branch_id' => $this->branch_id,
            'language' => $this->language,
        ];
    }
}
