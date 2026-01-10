<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalNote extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'color',
        'is_pinned',
        'pinned_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'pinned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getCategories(): array
    {
        return [
            'general' => ['label' => 'Umum', 'icon' => 'fa-sticky-note', 'color' => 'gray'],
            'work' => ['label' => 'Kerja', 'icon' => 'fa-briefcase', 'color' => 'blue'],
            'personal' => ['label' => 'Pribadi', 'icon' => 'fa-user', 'color' => 'purple'],
            'ideas' => ['label' => 'Ide', 'icon' => 'fa-lightbulb', 'color' => 'yellow'],
        ];
    }

    public static function getColors(): array
    {
        return [
            'gray' => ['bg' => 'bg-gray-100', 'border' => 'border-gray-200', 'text' => 'text-gray-700'],
            'blue' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700'],
            'green' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700'],
            'yellow' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700'],
            'red' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-700'],
            'purple' => ['bg' => 'bg-violet-50', 'border' => 'border-violet-200', 'text' => 'text-violet-700'],
            'pink' => ['bg' => 'bg-pink-50', 'border' => 'border-pink-200', 'text' => 'text-pink-700'],
        ];
    }

    public function getCategoryInfo(): array
    {
        return self::getCategories()[$this->category] ?? self::getCategories()['general'];
    }

    public function getColorClasses(): array
    {
        return self::getColors()[$this->color] ?? self::getColors()['gray'];
    }
}
