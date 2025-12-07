<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ethesis extends Model
{
    protected $fillable = [
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

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'skripsi' => 'Skripsi',
            'tesis' => 'Tesis',
            'disertasi' => 'Disertasi',
            default => ucfirst($this->type),
        };
    }
}
