<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'niy', 'nidn', 'nitk', 'name', 'front_title', 'back_title', 'full_name',
        'gender', 'birth_place_date', 'type', 'status', 'yayasan', 'category',
        'position', 'join_year', 'faculty', 'prodi', 'satker', 'campus',
        'education_level', 's1_univ', 's1_year', 's2_univ', 's2_year',
        's3_univ', 's3_year', 'expertise', 'email', 'serdos', 'domicile',
        'additional_duties', 'is_active'
    ];

    protected $casts = [
        'serdos' => 'boolean',
        'is_active' => 'boolean',
        'join_year' => 'integer',
        's1_year' => 'integer',
        's2_year' => 'integer',
        's3_year' => 'integer',
    ];

    public function scopeDosen($query)
    {
        return $query->where('type', 'dosen');
    }

    public function scopeTendik($query)
    {
        return $query->where('type', 'tendik');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->full_name) return $this->full_name;
        return trim(($this->front_title ?? '') . ' ' . $this->name . ', ' . ($this->back_title ?? ''));
    }
}
