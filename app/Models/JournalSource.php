<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalSource extends Model
{
    protected $fillable = [
        'code',
        'name',
        'base_url',
        'feed_type',
        'feed_url',
        'cover_url',
        'issn',
        'sinta_rank',
        'description',
        'is_active',
        'last_synced_at',
        'article_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
        'sinta_rank' => 'integer',
    ];

    public function articles()
    {
        return $this->hasMany(JournalArticle::class, 'journal_code', 'code');
    }

    /**
     * Get cover URL - returns stored cover or generates from base_url
     */
    public function getCoverUrlAttribute($value): ?string
    {
        if ($value) {
            return $value;
        }
        
        // Generate cover URL from OJS base URL pattern
        // OJS stores journal covers at: {base_url}/public/journals/{id}/cover_issue_{issue_id}.jpg
        // Or we can use the homepage image: {base_url}/public/journals/{id}/homepageImage_{lang}.jpg
        if ($this->base_url) {
            // Try common OJS cover patterns
            return rtrim($this->base_url, '/') . '/public/site/images/homepageImage_en_US.jpg';
        }
        
        return null;
    }
}

