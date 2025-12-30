<?php

namespace App\Notifications;

use App\Models\PlagiarismCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlagiarismCertificateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PlagiarismCheck $check) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Sertifikat Bebas Plagiasi Terbit - Perpustakaan UNIDA")
            ->greeting("Assalamu'alaikum {$notifiable->name},")
            ->line("Sertifikat Bebas Plagiasi Anda telah **diterbitkan**.")
            ->line("**Nomor Sertifikat:** {$this->check->certificate_number}")
            ->line("**Judul Dokumen:** {$this->check->document_title}")
            ->line("**Skor Similarity:** {$this->check->similarity_score}%")
            ->action('Lihat Sertifikat', url("/member/plagiarism/{$this->check->id}/certificate"))
            ->line('Terima kasih telah menggunakan layanan Perpustakaan UNIDA Gontor.');
    }
}
