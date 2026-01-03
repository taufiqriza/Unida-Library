<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Ethesis extends Model
{
    protected $fillable = [
        'source_type', 'external_id', 'external_url',
        'branch_id', 'department_id', 'title', 'title_en', 'abstract', 'abstract_en',
        'author', 'nim', 'advisor1', 'advisor2', 'examiner1', 'examiner2', 'examiner3',
        'year', 'defense_date', 'type', 'keywords', 'file_path', 'cover_path', 'url',
        'is_public', 'is_fulltext_public', 'views', 'downloads', 'user_id'
    ];

    protected $casts = [
        'defense_date' => 'date',
        'is_public' => 'boolean',
        'is_fulltext_public' => 'boolean',
    ];

    // Relasi ke branch yang input (untuk tracking, bukan filter)
    public function branch() { return $this->belongsTo(Branch::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function subjects() { return $this->belongsToMany(Subject::class, 'ethesis_subject'); }
    public function user() { return $this->belongsTo(User::class); }
    public function submission() { return $this->hasOne(ThesisSubmission::class); }
    
    public function fingerprint(): MorphOne
    {
        return $this->morphOne(DocumentFingerprint::class, 'documentable');
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'skripsi' => 'Skripsi',
            'tesis' => 'Tesis',
            'disertasi' => 'Disertasi',
            default => ucfirst($this->type),
        };
    }

    public function getCoverUrlAttribute(): string
    {
        if (!$this->cover_path) {
            return asset('storage/thesis.png');
        }
        
        $path = str_starts_with($this->cover_path, 'covers/') 
            ? 'thesis/' . $this->cover_path 
            : $this->cover_path;
            
        return asset('storage/' . $path);
    }
    
    public function getPreviewUrlAttribute(): ?string
    {
        $sub = $this->submission;
        if ($sub && $sub->preview_file && $sub->preview_visible) {
            return asset('storage/thesis/' . $sub->preview_file);
        }
        return null;
    }
    
    public function getFulltextUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;
        return asset('storage/thesis/' . $this->file_path);
    }
}
