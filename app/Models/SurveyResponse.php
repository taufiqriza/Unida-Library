<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyResponse extends Model
{
    protected $fillable = [
        'survey_id', 'member_id', 'respondent_type', 'respondent_name',
        'respondent_email', 'respondent_faculty', 'respondent_department',
        'ip_address', 'user_agent', 'is_complete', 'submitted_at'
    ];

    protected $casts = [
        'is_complete' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'response_id');
    }

    // Scopes
    public function scopeComplete($query)
    {
        return $query->where('is_complete', true);
    }

    // Accessors
    public function getRespondentDisplayNameAttribute(): string
    {
        if ($this->member) {
            return $this->member->name;
        }
        return $this->respondent_name ?: 'Anonim';
    }

    public function getRespondentTypeLabel(): string
    {
        return Survey::getRespondentTypes()[$this->respondent_type] ?? $this->respondent_type ?? '-';
    }

    public function getTotalScoreAttribute(): int
    {
        return $this->answers->sum('score') ?? 0;
    }

    public function getMaxPossibleScoreAttribute(): int
    {
        return $this->survey->sections->sum(fn($s) => $s->questions->count() * 5);
    }

    public function getPercentageScoreAttribute(): float
    {
        $max = $this->max_possible_score;
        return $max > 0 ? round(($this->total_score / $max) * 100, 1) : 0;
    }

    // Methods
    public function markComplete(): void
    {
        $this->update([
            'is_complete' => true,
            'submitted_at' => now(),
        ]);
        
        $this->survey->updateResponseCount();
    }
}
