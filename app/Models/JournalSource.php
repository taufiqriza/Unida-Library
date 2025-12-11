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
        'is_active',
        'last_synced_at',
        'article_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function articles()
    {
        return $this->hasMany(JournalArticle::class, 'journal_code', 'code');
    }
}
