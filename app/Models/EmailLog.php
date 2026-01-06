<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = ['campaign_id', 'recipient_id', 'email', 'subject', 'status', 'error_message', 'sent_at'];
    protected $casts = ['sent_at' => 'datetime'];

    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function recipient()
    {
        return $this->belongsTo(EmailRecipient::class, 'recipient_id');
    }
}
