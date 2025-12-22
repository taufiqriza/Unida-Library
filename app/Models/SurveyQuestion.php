<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'section_id', 'text', 'type', 'options', 'min_value', 'max_value',
        'is_required', 'order'
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    // Relationships
    public function section(): BelongsTo
    {
        return $this->belongsTo(SurveySection::class, 'section_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id');
    }

    // Accessors
    public function getSurveyAttribute()
    {
        return $this->section->survey;
    }

    public function getMaxScoreAttribute(): int
    {
        return match($this->type) {
            'likert' => $this->max_value ?? 5,
            'rating' => $this->max_value ?? 5,
            default => 0,
        };
    }

    // Static helpers
    public static function getTypes(): array
    {
        return [
            'likert' => 'Skala Likert (1-5)',
            'text' => 'Teks Bebas',
            'select' => 'Pilihan Ganda',
            'rating' => 'Rating',
            'number' => 'Angka',
        ];
    }

    public static function getLikertLabels(): array
    {
        return [
            1 => 'Sangat Tidak Setuju',
            2 => 'Tidak Setuju',
            3 => 'Cukup / Netral',
            4 => 'Setuju',
            5 => 'Sangat Setuju',
        ];
    }
}
