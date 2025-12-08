<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    protected $fillable = [
        'branch_id', 'user_id', 'title', 'sor', 'publisher_id', 'publish_year',
        'isbn', 'edition', 'pages', 'file_size', 'file_format', 'file_path',
        'cover_image', 'language', 'abstract', 'classification', 'call_number',
        'media_type_id', 'content_type_id', 'access_type', 'download_count',
        'view_count', 'is_active', 'opac_hide'
    ];

    protected $casts = ['is_active' => 'boolean'];

    // Relasi ke branch yang input (untuk tracking, bukan filter)
    public function branch() { return $this->belongsTo(Branch::class); }
    public function authors() { return $this->belongsToMany(Author::class, 'ebook_author'); }
    public function subjects() { return $this->belongsToMany(Subject::class, 'ebook_subject'); }
    public function downloads() { return $this->hasMany(EbookDownload::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getAuthorNamesAttribute(): string
    {
        return $this->authors->pluck('name')->implode('; ');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }
}
