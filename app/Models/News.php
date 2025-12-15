<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = [
        'branch_id', 'news_category_id', 'user_id', 'title', 'slug', 'excerpt',
        'content', 'featured_image', 'external_image', 'status', 'is_featured', 'is_pinned',
        'published_at', 'views'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->slug = $model->slug ?: Str::slug($model->title));
    }

    // Relasi ke branch yang input (untuk tracking, bukan filter)
    public function branch() { return $this->belongsTo(Branch::class); }
    public function category() { return $this->belongsTo(NewsCategory::class, 'news_category_id'); }
    public function author() { return $this->belongsTo(User::class, 'user_id'); }

    public function scopePublished($query) { return $query->where('status', 'published')->where('published_at', '<=', now()); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
    public function scopePinned($query) { return $query->where('is_pinned', true); }

    public function getImageUrlAttribute(): ?string
    {
        // Prioritize local storage first (downloaded images)
        if ($this->featured_image) {
            return asset('storage/' . $this->featured_image);
        }
        // Fallback to external image (WordPress URL) if no local copy
        return $this->external_image ?: null;
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' && $this->published_at <= now();
    }
}
