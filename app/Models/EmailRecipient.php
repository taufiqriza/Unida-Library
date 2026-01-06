<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailRecipient extends Model
{
    protected $fillable = ['name', 'email', 'category', 'phone', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function logs()
    {
        return $this->hasMany(EmailLog::class, 'recipient_id');
    }
}
