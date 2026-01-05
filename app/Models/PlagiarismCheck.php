<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PlagiarismCheck extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const PROVIDER_INTERNAL = 'internal';
    const PROVIDER_ITHENTICATE = 'ithenticate';
    const PROVIDER_TURNITIN = 'turnitin';
    const PROVIDER_COPYLEAKS = 'copyleaks';

    protected $fillable = [
        'member_id',
        'thesis_submission_id',
        'document_title',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
        'word_count',
        'page_count',
        'status',
        'check_type',
        'external_platform',
        'external_report_file',
        'similarity_score',
        'similarity_sources',
        'detailed_report',
        'provider',
        'external_id',
        'external_report_url',
        'certificate_number',
        'certificate_path',
        'certificate_generated_at',
        'started_at',
        'completed_at',
        'error_message',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'similarity_sources' => 'array',
        'detailed_report' => 'array',
        'similarity_score' => 'decimal:2',
        'certificate_generated_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // ========== Relationships ==========

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class)->withoutGlobalScope('branch');
    }

    public function thesisSubmission(): BelongsTo
    {
        return $this->belongsTo(ThesisSubmission::class);
    }

    public function fingerprint(): MorphOne
    {
        return $this->morphOne(DocumentFingerprint::class, 'documentable');
    }
    
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ========== Type Helpers ==========
    
    public function isExternal(): bool
    {
        return $this->check_type === 'external';
    }
    
    public function isSystem(): bool
    {
        return $this->check_type === 'system';
    }
    
    public function getExternalPlatformLabel(): string
    {
        return match($this->external_platform) {
            'turnitin' => 'Turnitin',
            'ithenticate' => 'iThenticate',
            'copyscape' => 'Copyscape',
            default => $this->external_platform ?? '-',
        };
    }

    // ========== Status Helpers ==========

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function hasCertificate(): bool
    {
        return !empty($this->certificate_number);
    }

    /**
     * Check if process seems stuck (no progress for too long)
     */
    public function isStuck(): bool
    {
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING])) {
            return false;
        }

        // Pending > 30 minutes without being picked up
        if ($this->status === self::STATUS_PENDING && $this->created_at->diffInMinutes(now()) > 30) {
            return true;
        }

        // Processing > 60 minutes (even large docs should finish)
        if ($this->status === self::STATUS_PROCESSING && $this->started_at && $this->started_at->diffInMinutes(now()) > 60) {
            return true;
        }

        return false;
    }

    /**
     * Get detailed status info for UI
     */
    public function getStatusInfoAttribute(): array
    {
        // External submissions have different status labels
        if ($this->check_type === 'external' && $this->status === self::STATUS_PENDING) {
            return [
                'status' => 'pending_review',
                'label' => 'Menunggu Review',
                'message' => 'Pengajuan sedang direview oleh pustakawan.',
                'color' => 'violet',
                'icon' => 'user-clock',
            ];
        }

        if ($this->isStuck()) {
            return [
                'status' => 'stuck',
                'label' => 'Proses Terhenti',
                'message' => 'Proses memakan waktu lebih lama dari biasanya. Silakan hubungi petugas perpustakaan.',
                'color' => 'red',
                'icon' => 'exclamation-circle',
            ];
        }

        return match($this->status) {
            self::STATUS_PENDING => [
                'status' => 'pending',
                'label' => 'Menunggu Antrian',
                'message' => 'Dokumen Anda dalam antrian dan akan segera diproses.',
                'color' => 'gray',
                'icon' => 'clock',
            ],
            self::STATUS_PROCESSING => [
                'status' => 'processing',
                'label' => 'Sedang Diproses',
                'message' => 'Dokumen sedang dicek. Proses ini memakan waktu 5-15 menit untuk dokumen besar.',
                'color' => 'blue',
                'icon' => 'cog',
            ],
            self::STATUS_COMPLETED => [
                'status' => 'completed',
                'label' => 'Selesai',
                'message' => 'Pengecekan selesai.',
                'color' => 'green',
                'icon' => 'check-circle',
            ],
            self::STATUS_FAILED => [
                'status' => 'failed',
                'label' => 'Gagal Diproses',
                'message' => 'Terjadi kendala saat memproses. Silakan coba lagi atau hubungi petugas.',
                'color' => 'amber',
                'icon' => 'exclamation-triangle',
            ],
            default => [
                'status' => 'unknown',
                'label' => 'Status Tidak Diketahui',
                'message' => '',
                'color' => 'gray',
                'icon' => 'question-mark-circle',
            ],
        };
    }

    // ========== Status Label & Color ==========

    public function getStatusLabelAttribute(): string
    {
        return $this->status_info['label'];
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'gray',
            self::STATUS_PROCESSING => $this->isStuck() ? 'danger' : 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'warning',
            default => 'gray',
        };
    }

    // ========== Similarity Score Helpers ==========

    public function getSimilarityLevelAttribute(): string
    {
        if ($this->similarity_score === null) {
            return 'unknown';
        }

        return match(true) {
            $this->similarity_score <= 15 => 'low',      // Rendah (Aman)
            $this->similarity_score <= 25 => 'moderate', // Sedang
            $this->similarity_score <= 40 => 'high',     // Tinggi
            default => 'critical',                        // Sangat Tinggi
        };
    }

    public function getSimilarityColorAttribute(): string
    {
        return match($this->similarity_level) {
            'low' => 'success',
            'moderate' => 'warning',
            'high' => 'danger',
            'critical' => 'danger',
            default => 'gray',
        };
    }

    public function getSimilarityLabelAttribute(): string
    {
        return match($this->similarity_level) {
            'low' => 'Rendah (Aman)',
            'moderate' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Sangat Tinggi',
            default => '-',
        };
    }

    public function isPassed(): bool
    {
        $threshold = (float) Setting::get('plagiarism_pass_threshold', 25);
        return $this->similarity_score !== null && $this->similarity_score <= $threshold;
    }

    // ========== Certificate Helpers ==========

    public static function generateCertificateNumber(): string
    {
        $prefix = 'PLAG';
        $yearMonth = date('Ym');
        
        // Get highest certificate number this month
        $lastNumber = static::where('certificate_number', 'like', "PLAG-{$yearMonth}-%")
            ->selectRaw('MAX(CAST(SUBSTRING(certificate_number, -5) AS UNSIGNED)) as max_num')
            ->value('max_num') ?? 0;

        return sprintf('%s-%s-%05d', $prefix, $yearMonth, $lastNumber + 1);
    }

    // ========== File Helpers ==========

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    public function getProcessingTimeAttribute(): ?string
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        $diff = $this->started_at->diffInSeconds($this->completed_at);
        
        if ($diff >= 60) {
            return floor($diff / 60) . ' menit ' . ($diff % 60) . ' detik';
        }
        
        return $diff . ' detik';
    }

    // ========== Provider Helpers ==========

    public function getProviderLabelAttribute(): string
    {
        return match($this->provider) {
            self::PROVIDER_INTERNAL => 'Internal (E-Thesis Database)',
            self::PROVIDER_ITHENTICATE => 'Turnitin / iThenticate',
            self::PROVIDER_TURNITIN => 'Turnitin',
            self::PROVIDER_COPYLEAKS => 'Copyleaks',
            default => $this->provider,
        };
    }

    // ========== Scopes ==========

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeForMember($query, int $memberId)
    {
        return $query->where('member_id', $memberId);
    }
}
