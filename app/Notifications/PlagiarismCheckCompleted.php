<?php

namespace App\Notifications;

use App\Models\PlagiarismCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlagiarismCheckCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PlagiarismCheck $check) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $score = $this->check->similarity_score ?? 0;
        $title = $this->check->document_title ?? $this->check->title ?? 'Dokumen';
        
        $subject = $this->check->status === 'completed' 
            ? "📊 Hasil Cek Plagiasi: {$score}% - UNIDA Library"
            : "❌ Cek Plagiasi Gagal - UNIDA Library";

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.plagiarism-result', [
                'name' => $notifiable->name,
                'status' => $this->check->status,
                'documentTitle' => $title,
                'score' => $score,
                'errorMessage' => $this->check->error_message,
                'detailUrl' => url('/member/plagiarism-check/' . $this->check->id),
            ]);
    }
}
