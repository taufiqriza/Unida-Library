<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PddiktiData extends Model
{
    protected $table = 'pddikti_data';

    protected $fillable = [
        'pddikti_id',
        'type',
        'name',
        'nim_nidn',
        'prodi',
        'prodi_code',
        'jenjang',
        'status',
        'angkatan',
        'pt_name',
        'pt_id',
        'synced_at',
        'linked_member_id',
        'linked_at',
    ];

    protected $casts = [
        'angkatan' => 'integer',
        'synced_at' => 'datetime',
        'linked_at' => 'datetime',
    ];

    /**
     * Get the linked member
     */
    public function linkedMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'linked_member_id');
    }

    /**
     * Check if this record is already linked
     */
    public function isLinked(): bool
    {
        return $this->linked_member_id !== null;
    }

    /**
     * Scope for mahasiswa only
     */
    public function scopeMahasiswa($query)
    {
        return $query->where('type', 'mahasiswa');
    }

    /**
     * Scope for dosen only
     */
    public function scopeDosen($query)
    {
        return $query->where('type', 'dosen');
    }

    /**
     * Scope for active students/lecturers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Scope for unlinked records
     */
    public function scopeUnlinked($query)
    {
        return $query->whereNull('linked_member_id');
    }

    /**
     * Search by name (case-insensitive, fuzzy)
     */
    public function scopeSearchByName($query, string $name)
    {
        $name = trim($name);
        if (empty($name)) {
            return $query;
        }

        // Split name into words for better matching
        $words = explode(' ', $name);
        
        return $query->where(function($q) use ($name, $words) {
            // Exact match has highest priority
            $q->where('name', 'like', "%{$name}%");
            
            // Also match each word
            foreach ($words as $word) {
                if (strlen($word) >= 2) {
                    $q->orWhere('name', 'like', "%{$word}%");
                }
            }
        });
    }

    /**
     * Get display label for this record
     */
    public function getDisplayLabelAttribute(): string
    {
        $label = $this->name;
        
        if ($this->nim_nidn) {
            $label .= " ({$this->nim_nidn})";
        }
        
        if ($this->prodi) {
            $label .= " - {$this->prodi}";
        }
        
        if ($this->jenjang) {
            $label .= " ({$this->jenjang})";
        }

        return $label;
    }

    /**
     * Get type badge color
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->type) {
            'mahasiswa' => 'blue',
            'dosen' => 'emerald',
            default => 'gray',
        };
    }
}
