<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalArticle extends Model
{
    protected $fillable = [
        'source_type',
        'external_id',
        'journal_code',
        'journal_name',
        'title',
        'abstract',
        'abstract_en',
        'authors',
        'doi',
        'volume',
        'issue',
        'issue_title',
        'pages',
        'publish_year',
        'published_at',
        'url',
        'pdf_url',
        'cover_url',
        'keywords',
        'language',
        'rights',
        'views',
        'synced_at',
    ];

    protected $casts = [
        'authors' => 'array',
        'keywords' => 'array',
        'published_at' => 'date',
        'synced_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(JournalSource::class, 'journal_code', 'code');
    }

    /**
     * Get cover URL - returns article cover or falls back to journal source cover
     */
    public function getCoverUrlAttribute($value): ?string
    {
        // If article has its own cover, use it
        if ($value) {
            return $value;
        }
        
        // Fall back to journal source cover
        if ($this->source) {
            return $this->source->cover_url;
        }
        
        return null;
    }

    public function getAuthorsStringAttribute(): string
    {
        if (empty($this->authors)) return '';
        return collect($this->authors)->pluck('name')->implode(', ');
    }

    public function getKeywordsStringAttribute(): string
    {
        if (empty($this->keywords)) return '';
        return implode(', ', $this->keywords);
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->whereFullText(['title', 'abstract'], $term)
            ->orWhere('title', 'like', "%{$term}%");
    }
}
