<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Survey extends Model
{
    protected $fillable = [
        'branch_id', 'created_by', 'title', 'slug', 'description',
        'status', 'start_date', 'end_date', 'is_anonymous', 'require_login',
        'target_groups', 'response_count'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_anonymous' => 'boolean',
        'require_login' => 'boolean',
        'target_groups' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($survey) {
            if (empty($survey->slug)) {
                $survey->slug = Str::slug($survey->title) . '-' . Str::random(6);
            }
        });
    }

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(SurveySection::class)->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // Accessors
    public function getIsOpenAttribute(): bool
    {
        if ($this->status !== 'active') return false;
        
        $now = now()->startOfDay();
        if ($this->start_date && $now < $this->start_date) return false;
        if ($this->end_date && $now > $this->end_date) return false;
        
        return true;
    }

    public function getQuestionsCountAttribute(): int
    {
        return $this->sections->sum(fn($s) => $s->questions->count());
    }

    public function getCompletedResponsesCountAttribute(): int
    {
        return $this->responses()->where('is_complete', true)->count();
    }

    // Methods
    public function updateResponseCount(): void
    {
        $this->update([
            'response_count' => $this->responses()->where('is_complete', true)->count()
        ]);
    }

    public function getAnalytics(): array
    {
        $responses = $this->responses()
            ->where('is_complete', true)
            ->with('answers.question.section')
            ->get();

        if ($responses->isEmpty()) {
            return ['sections' => [], 'overall' => null];
        }

        $sectionScores = [];
        
        foreach ($this->sections as $section) {
            $questionIds = $section->questions->pluck('id');
            $maxScore = $section->questions->count() * 5; // Max score per response
            
            $totalScore = 0;
            $responseCount = 0;
            
            foreach ($responses as $response) {
                $sectionAnswers = $response->answers->whereIn('question_id', $questionIds);
                if ($sectionAnswers->isNotEmpty()) {
                    $totalScore += $sectionAnswers->sum('score');
                    $responseCount++;
                }
            }
            
            $avgScore = $responseCount > 0 ? $totalScore / $responseCount : 0;
            $maxPossible = $maxScore;
            $percentage = $maxPossible > 0 ? ($avgScore / $maxPossible) * 100 : 0;
            
            $sectionScores[$section->id] = [
                'name' => $section->name,
                'score' => round($avgScore, 2),
                'max_score' => $maxPossible,
                'percentage' => round($percentage, 1),
                'category' => $this->getCategory($percentage),
            ];
        }

        // Overall score
        $overallScore = array_sum(array_column($sectionScores, 'score'));
        $overallMax = array_sum(array_column($sectionScores, 'max_score'));
        $overallPercentage = $overallMax > 0 ? ($overallScore / $overallMax) * 100 : 0;

        return [
            'sections' => $sectionScores,
            'overall' => [
                'score' => round($overallScore, 2),
                'max_score' => $overallMax,
                'percentage' => round($overallPercentage, 1),
                'category' => $this->getCategory($overallPercentage),
            ],
            'response_count' => $responses->count(),
        ];
    }

    public static function getCategory(float $percentage): string
    {
        return match(true) {
            $percentage >= 81 => 'Sangat Memuaskan',
            $percentage >= 61 => 'Memuaskan',
            $percentage >= 41 => 'Cukup',
            $percentage >= 21 => 'Kurang Memuaskan',
            default => 'Tidak Memuaskan',
        };
    }

    public static function getCategoryColor(string $category): string
    {
        return match($category) {
            'Sangat Memuaskan' => 'emerald',
            'Memuaskan' => 'green',
            'Cukup' => 'yellow',
            'Kurang Memuaskan' => 'orange',
            'Tidak Memuaskan' => 'red',
            default => 'gray',
        };
    }

    public static function getRespondentTypes(): array
    {
        return [
            'mahasiswa_s1' => 'Mahasiswa S1',
            'mahasiswa_s2' => 'Mahasiswa S2',
            'mahasiswa_s3' => 'Mahasiswa S3',
            'dosen' => 'Dosen',
            'tendik' => 'Tendik / Staf',
            'tamu' => 'Tamu',
            'pku' => 'Program Kaderisasi Ulama',
            'lainnya' => 'Lainnya',
        ];
    }
}
