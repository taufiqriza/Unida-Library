<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    protected $fillable = [
        'branch_id', 'title', 'description', 'file_path', 'cover_image', 
        'file_format', 'file_size', 'isbn', 'publish_year', 'language', 
        'access_type', 'view_count', 'is_active', 'user_id'
    ];

    protected $casts = ['is_active' => 'boolean'];

    // Relasi ke branch yang input (untuk tracking, bukan filter)
    public function branch() { return $this->belongsTo(Branch::class); }
    public function authors() { return $this->belongsToMany(Author::class, 'ebook_author'); }
    public function subjects() { return $this->belongsToMany(Subject::class, 'ebook_subject'); }
    public function downloads() { return $this->hasMany(EbookDownload::class); }
    public function user() { return $this->belongsTo(User::class); }
}
