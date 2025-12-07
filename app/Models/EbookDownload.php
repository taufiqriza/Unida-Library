<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookDownload extends Model
{
    protected $fillable = ['ebook_id', 'member_id', 'user_id', 'ip_address', 'user_agent'];

    public function ebook() { return $this->belongsTo(Ebook::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function user() { return $this->belongsTo(User::class); }
}
