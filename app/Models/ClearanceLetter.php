<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClearanceLetter extends Model
{
    protected $fillable = [
        'member_id',
        'thesis_submission_id',
        'letter_number',
        'purpose',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'file_path',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function thesisSubmission(): BelongsTo
    {
        return $this->belongsTo(ThesisSubmission::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generateLetterNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = sprintf('SBP/%s/%s/', $month, $year);
        
        $lastNumber = static::where('letter_number', 'like', $prefix . '%')
            ->orderByDesc('letter_number')
            ->value('letter_number');
        
        $nextSeq = 1;
        if ($lastNumber) {
            $lastSeq = (int) substr($lastNumber, -4);
            $nextSeq = $lastSeq + 1;
        }
        
        return sprintf('%s%04d', $prefix, $nextSeq);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function generateMemberSignatureQr(): string
    {
        $data = json_encode(['d'=>'SBP','i'=>$this->id,'n'=>$this->member->member_id,'h'=>substr(md5('M'.$this->id.$this->member_id),0,8)]);
        $qr = QrCode::format('svg')->size(100)->margin(0)->generate($data);
        return 'data:image/svg+xml;base64,' . base64_encode($qr);
    }

    public function generateApproverSignatureQr(): string
    {
        $data = json_encode(['d'=>'SBP','i'=>$this->id,'a'=>$this->approved_by,'h'=>substr(md5('A'.$this->id.$this->approved_by),0,8)]);
        $qr = QrCode::format('svg')->size(100)->margin(0)->generate($data);
        return 'data:image/svg+xml;base64,' . base64_encode($qr);
    }
}
