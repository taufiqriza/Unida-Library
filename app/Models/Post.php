<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'slug', 'excerpt', 'content', 'image', 'status', 'published_at'];

    protected $casts = ['published_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopePublished($query) { return $query->where('status', 'published')->whereNotNull('published_at'); }
}
