<?php

namespace App\Models;

use App\Enums\ThesisType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ThesisSubmission extends Model
{
    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_REVISION_REQUIRED = 'revision_required';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'member_id', 'department_id', 'ethesis_id',
        'type', 'title', 'title_en', 'abstract', 'abstract_en', 'keywords',
        'author', 'nim',
        'advisor1', 'advisor2', 'examiner1', 'examiner2', 'examiner3',
        'year', 'defense_date',
        'cover_file', 'approval_file', 'preview_file', 'fulltext_file',
        'status', 'reviewed_by', 'reviewed_at', 'review_notes', 'rejection_reason',
        'cover_visible', 'approval_visible', 'preview_visible', 'fulltext_visible',
        'allow_fulltext_public',
    ];

    protected $casts = [
        'defense_date' => 'date',
        'reviewed_at' => 'datetime',
        'cover_visible' => 'boolean',
        'approval_visible' => 'boolean',
        'preview_visible' => 'boolean',
        'fulltext_visible' => 'boolean',
        'allow_fulltext_public' => 'boolean',
    ];

    // Relations
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function ethesis(): BelongsTo
    {
        return $this->belongsTo(Ethesis::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ThesisSubmissionLog::class, 'submission_id');
    }

    public function clearanceLetter()
    {
        return $this->hasOne(ClearanceLetter::class);
    }

    // Enum accessor
    public function getThesisTypeEnum(): ?ThesisType
    {
        return ThesisType::tryFrom($this->type);
    }

    // Status helpers
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Diajukan',
            self::STATUS_UNDER_REVIEW => 'Sedang Direview',
            self::STATUS_REVISION_REQUIRED => 'Perlu Revisi',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_PUBLISHED => 'Dipublikasikan',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SUBMITTED => 'info',
            self::STATUS_UNDER_REVIEW => 'warning',
            self::STATUS_REVISION_REQUIRED => 'orange',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_PUBLISHED => 'primary',
            default => 'gray',
        };
    }

    public function getTypeLabel(): string
    {
        return $this->getThesisTypeEnum()?->label() ?? ucfirst($this->type);
    }

    public function getTypeDegree(): string
    {
        return $this->getThesisTypeEnum()?->degree() ?? '';
    }

    public function getTypeFullLabel(): string
    {
        return $this->getThesisTypeEnum()?->fullLabel() ?? ucfirst($this->type);
    }

    // Status checks
    public function isDraft(): bool { return $this->status === self::STATUS_DRAFT; }
    public function isSubmitted(): bool { return $this->status === self::STATUS_SUBMITTED; }
    public function isUnderReview(): bool { return $this->status === self::STATUS_UNDER_REVIEW; }
    public function isRevisionRequired(): bool { return $this->status === self::STATUS_REVISION_REQUIRED; }
    public function isApproved(): bool { return $this->status === self::STATUS_APPROVED; }
    public function isRejected(): bool { return $this->status === self::STATUS_REJECTED; }
    public function isPublished(): bool { return $this->status === self::STATUS_PUBLISHED; }

    public function canEdit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVISION_REQUIRED]);
    }

    public function canSubmit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVISION_REQUIRED]);
    }

    public function canReview(): bool
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW]);
    }

    // File access control
    public function canAccessFile(string $fileType, ?Member $member = null, ?User $user = null): bool
    {
        // Admin always has access
        if ($user) {
            return true;
        }

        // Owner always has access
        if ($member && $this->member_id === $member->id) {
            return true;
        }

        // Not published yet - only owner and admin
        if (!$this->isPublished()) {
            return false;
        }

        // Check visibility settings
        return match($fileType) {
            'cover' => $this->cover_visible,
            'approval' => $this->approval_visible,
            'preview' => $this->preview_visible,
            'fulltext' => $this->fulltext_visible || $this->allow_fulltext_public,
            default => false,
        };
    }

    public function getFileUrl(string $fileType): ?string
    {
        $file = match($fileType) {
            'cover' => $this->cover_file,
            'approval' => $this->approval_file,
            'preview' => $this->preview_file,
            'fulltext' => $this->fulltext_file,
            default => null,
        };

        return $file ? route('thesis.file', ['submission' => $this->id, 'type' => $fileType]) : null;
    }

    public function getSecureFileUrl(string $fileType): ?string
    {
        $file = match($fileType) {
            'cover' => $this->cover_file,
            'approval' => $this->approval_file,
            'preview' => $this->preview_file,
            'fulltext' => $this->fulltext_file,
            default => null,
        };

        if (!$file) return null;

        // For now, use public storage URL (can be changed to signed URL later)
        return Storage::disk('public')->url($file);
    }

    // Actions
    public function submit(?int $memberId = null): void
    {
        $fromStatus = $this->status;
        $this->status = self::STATUS_SUBMITTED;
        $this->save();

        $this->logAction('submitted', $fromStatus, self::STATUS_SUBMITTED, null, $memberId);
    }

    public function startReview(int $userId): void
    {
        $fromStatus = $this->status;
        $this->status = self::STATUS_UNDER_REVIEW;
        $this->reviewed_by = $userId;
        $this->save();

        $this->logAction('review_started', $fromStatus, self::STATUS_UNDER_REVIEW, null, null, $userId);
    }

    public function approve(int $userId, ?string $notes = null): void
    {
        $fromStatus = $this->status;
        $this->status = self::STATUS_APPROVED;
        $this->reviewed_by = $userId;
        $this->reviewed_at = now();
        $this->review_notes = $notes;
        $this->save();

        $this->logAction('approved', $fromStatus, self::STATUS_APPROVED, $notes, null, $userId);
    }

    public function reject(int $userId, string $reason): void
    {
        $fromStatus = $this->status;
        $this->status = self::STATUS_REJECTED;
        $this->reviewed_by = $userId;
        $this->reviewed_at = now();
        $this->rejection_reason = $reason;
        $this->save();

        $this->logAction('rejected', $fromStatus, self::STATUS_REJECTED, $reason, null, $userId);
    }

    public function requestRevision(int $userId, string $notes): void
    {
        $fromStatus = $this->status;
        $this->status = self::STATUS_REVISION_REQUIRED;
        $this->reviewed_by = $userId;
        $this->reviewed_at = now();
        $this->review_notes = $notes;
        $this->save();

        $this->logAction('revision_requested', $fromStatus, self::STATUS_REVISION_REQUIRED, $notes, null, $userId);
    }

    public function publish(int $userId): ?Ethesis
    {
        if (!$this->isApproved()) {
            return null;
        }

        // Only copy cover to public storage (for display in search results)
        // Preview and fulltext stay in private storage, accessed via controller with permission check
        $publicCoverPath = $this->copyFileToPublic($this->cover_file, 'covers');

        // Create Ethesis record
        $ethesis = Ethesis::create([
            'department_id' => $this->department_id,
            'title' => $this->title,
            'title_en' => $this->title_en,
            'abstract' => $this->abstract,
            'abstract_en' => $this->abstract_en,
            'author' => $this->author,
            'nim' => $this->nim,
            'advisor1' => $this->advisor1,
            'advisor2' => $this->advisor2,
            'examiner1' => $this->examiner1,
            'examiner2' => $this->examiner2,
            'examiner3' => $this->examiner3,
            'year' => $this->year,
            'defense_date' => $this->defense_date,
            'type' => $this->type,
            'keywords' => $this->keywords,
            'file_path' => $this->fulltext_file,      // Keep in thesis/ storage (private)
            'cover_path' => $publicCoverPath,          // Public for display
            'preview_path' => $this->preview_file,     // Keep in thesis/ storage (private)
            'is_public' => true,
            'is_fulltext_public' => $this->allow_fulltext_public || $this->fulltext_visible,
            'user_id' => $userId,
        ]);

        $fromStatus = $this->status;
        $this->status = self::STATUS_PUBLISHED;
        $this->ethesis_id = $ethesis->id;
        $this->save();

        $this->logAction('published', $fromStatus, self::STATUS_PUBLISHED, null, null, $userId);

        return $ethesis;
    }

    /**
     * Copy file from thesis private storage to public storage
     */
    protected function copyFileToPublic(?string $sourcePath, string $publicFolder): ?string
    {
        if (!$sourcePath) {
            return null;
        }

        // Source is in thesis/ disk
        $sourceFullPath = storage_path('app/thesis/' . $sourcePath);
        
        if (!file_exists($sourceFullPath)) {
            // File doesn't exist in thesis/, check if already in public
            $publicPath = storage_path('app/public/' . $sourcePath);
            if (file_exists($publicPath)) {
                return $sourcePath; // Already in public
            }
            return null;
        }

        // Get just the filename
        $filename = basename($sourcePath);
        
        // Destination path in public storage
        $destPath = $publicFolder . '/' . $filename;
        $destFullPath = storage_path('app/public/' . $destPath);

        // Ensure destination directory exists
        $destDir = dirname($destFullPath);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // Copy the file
        copy($sourceFullPath, $destFullPath);

        return $destPath;
    }

    protected function logAction(string $action, ?string $fromStatus, string $toStatus, ?string $notes = null, ?int $memberId = null, ?int $userId = null): void
    {
        $this->logs()->create([
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
            'member_id' => $memberId,
            'user_id' => $userId,
        ]);
    }

    // File info helpers
    public function getFilesInfo(): array
    {
        return [
            'cover' => [
                'exists' => (bool) $this->cover_file,
                'visible' => $this->cover_visible,
                'label' => 'Cover',
                'icon' => 'fa-image',
                'required' => true,
            ],
            'approval' => [
                'exists' => (bool) $this->approval_file,
                'visible' => $this->approval_visible,
                'label' => 'Lembar Pengesahan',
                'icon' => 'fa-file-signature',
                'required' => true,
            ],
            'preview' => [
                'exists' => (bool) $this->preview_file,
                'visible' => $this->preview_visible,
                'label' => 'BAB 1-3',
                'icon' => 'fa-file-alt',
                'required' => true,
            ],
            'fulltext' => [
                'exists' => (bool) $this->fulltext_file,
                'visible' => $this->fulltext_visible || $this->allow_fulltext_public,
                'label' => 'Full Text',
                'icon' => 'fa-file-pdf',
                'required' => false,
            ],
        ];
    }
}
