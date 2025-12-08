<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThesisSubmissionLog extends Model
{
    protected $fillable = [
        'submission_id',
        'user_id',
        'member_id',
        'action',
        'from_status',
        'to_status',
        'notes',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ThesisSubmission::class, 'submission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Dibuat',
            'submitted' => 'Diajukan',
            'review_started' => 'Mulai Direview',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision_requested' => 'Diminta Revisi',
            'revised' => 'Direvisi',
            'published' => 'Dipublikasikan',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    public function getActorNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name . ' (Pustakawan)';
        }
        if ($this->member) {
            return $this->member->name;
        }
        return 'System';
    }
}
