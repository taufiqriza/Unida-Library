<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyAnswer extends Model
{
    protected $fillable = [
        'response_id', 'question_id', 'answer', 'score'
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // Relationships
    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class, 'response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    // Accessors
    public function getScoreLabelAttribute(): string
    {
        if ($this->question?->type !== 'likert') {
            return $this->answer ?? '-';
        }
        
        return SurveyQuestion::getLikertLabels()[$this->score] ?? (string) $this->score;
    }
}
