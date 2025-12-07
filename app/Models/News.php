<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = [
        'branch_id', 'news_category_id', 'user_id', 'title', 'slug', 'excerpt',
        'content', 'featured_image', 'status', 'is_featured', 'is_pinned',
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
}
