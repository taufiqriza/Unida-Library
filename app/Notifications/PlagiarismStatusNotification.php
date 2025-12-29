<?php

namespace App\Notifications;

use App\Models\PlagiarismCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlagiarismStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PlagiarismCheck $check) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Hasil Cek Plagiasi - Perpustakaan UNIDA")
            ->greeting("Assalamu'alaikum {$notifiable->name},");

        if ($this->check->status === 'completed') {
            $similarity = $this->check->similarity_score ?? 0;
            $message->line("Pengecekan plagiasi dokumen Anda telah **selesai**.")
                ->line("**Dokumen:** {$this->check->title}")
                ->line("**Tingkat Kemiripan:** {$similarity}%")
                ->action('Lihat Hasil', url("/member/plagiarism/{$this->check->id}"));
        } else {
            $message->line("Status pengecekan plagiasi: **" . ucfirst($this->check->status) . "**")
                ->line("**Dokumen:** {$this->check->title}")
                ->action('Lihat Detail', url("/member/plagiarism/{$this->check->id}"));
        }

        return $message->line('Terima kasih telah menggunakan layanan Perpustakaan UNIDA Gontor.');
    }
}
