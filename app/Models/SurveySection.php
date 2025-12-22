<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveySection extends Model
{
    protected $fillable = [
        'survey_id', 'name', 'name_en', 'description', 'order'
    ];

    // Relationships
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class, 'section_id')->orderBy('order');
    }

    // Accessors
    public function getQuestionsCountAttribute(): int
    {
        return $this->questions->count();
    }
}
