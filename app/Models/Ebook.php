<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    protected $fillable = [
        'branch_id', 'user_id', 'title', 'sor', 'publisher_id', 'publish_year',
        'isbn', 'edition', 'pages', 'file_size', 'file_format', 'file_path',
        'cover_image', 'language', 'abstract', 'classification', 'call_number',
        'media_type_id', 'content_type_id', 'digital_category_id', 'collection_type',
        'access_type', 'download_count', 'view_count', 'is_active', 'opac_hide',
        'is_downloadable'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_downloadable' => 'boolean',
        'opac_hide' => 'boolean',
    ];

    // Relations
    public function branch() { return $this->belongsTo(Branch::class); }
    public function authors() { return $this->belongsToMany(Author::class, 'ebook_author'); }
    public function subjects() { return $this->belongsToMany(Subject::class, 'ebook_subject'); }
    public function downloads() { return $this->hasMany(EbookDownload::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function digitalCategory() { return $this->belongsTo(DigitalCategory::class); }

    // Scopes
    public function scopeUniversitaria($query)
    {
        return $query->where('collection_type', 'universitaria');
    }

    public function scopeRegular($query)
    {
        return $query->whereNull('collection_type')->orWhere('collection_type', 'regular');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('opac_hide', false);
    }

    // Accessors
    public function getAuthorNamesAttribute(): string
    {
        return $this->authors->pluck('name')->implode('; ');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function isUniversitaria(): bool
    {
        return $this->collection_type === 'universitaria';
    }
}

