<?php

namespace App\Console\Commands;

use App\Models\ChatMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupOldVoiceNotes extends Command
{
    protected $signature = 'voice:cleanup';
    protected $description = 'Delete voice notes older than 5 months';

    public function handle()
    {
        $count = 0;
        ChatMessage::whereNotNull('voice_path')
            ->where('created_at', '<', now()->subMonths(5))
            ->chunk(100, function ($messages) use (&$count) {
                foreach ($messages as $msg) {
                    Storage::disk('public')->delete($msg->voice_path);
                    $msg->update(['voice_path' => null, 'voice_duration' => null]);
                    $count++;
                }
            });
        
        $this->info("Deleted {$count} old voice notes.");
    }
}
