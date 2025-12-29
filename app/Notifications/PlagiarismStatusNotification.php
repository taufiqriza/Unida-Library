<?php

namespace App\Notifications;

use App\Models\PlagiarismCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class PlagiarismStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PlagiarismCheck $check) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $data = [
            'name' => $notifiable->name,
            'status' => $this->check->status,
            'documentTitle' => $this->check->title ?? $this->check->document_title ?? 'Dokumen',
            'score' => $this->check->similarity_score ?? 0,
            'errorMessage' => $this->check->error_message,
            'detailUrl' => url('/member/plagiarism/' . $this->check->id),
        ];

        $score = $this->check->similarity_score ?? 0;
        
        Mail::send('emails.plagiarism-result', $data, function ($message) use ($notifiable, $score) {
            $subject = $this->check->status === 'completed' 
                ? "📊 Hasil Cek Plagiasi: {$score}% - UNIDA Library"
                : "❌ Cek Plagiasi Gagal - UNIDA Library";
            
            $message->to($notifiable->email)
                ->subject($subject);
        });

        return null;
    }
}
