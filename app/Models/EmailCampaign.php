<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    protected $fillable = ['name', 'subject', 'template', 'recipient_ids', 'status', 'total_recipients', 'sent_count', 'failed_count', 'created_by', 'sent_at'];
    protected $casts = ['recipient_ids' => 'array', 'sent_at' => 'datetime'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(EmailLog::class, 'campaign_id');
    }
}
