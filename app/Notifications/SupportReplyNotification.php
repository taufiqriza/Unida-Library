<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportReplyNotification extends Notification
{
    public function __construct(
        public string $staffName,
        public string $messagePreview,
        public ?string $topic = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $topicLabels = [
            'unggah' => 'Unggah Mandiri',
            'plagiasi' => 'Cek Plagiasi', 
            'bebas' => 'Bebas Pustaka',
            'pinjam' => 'Peminjaman',
            'lainnya' => 'Lainnya',
        ];
        
        $topicLabel = $topicLabels[$this->topic] ?? 'Support';
        
        return (new MailMessage)
            ->subject('Balasan dari Perpustakaan UNIDA Gontor')
            ->greeting("Halo {$notifiable->name}!")
            ->line("Staff kami ({$this->staffName}) telah membalas pertanyaan Anda terkait {$topicLabel}:")
            ->line("\"{$this->messagePreview}\"")
            ->action('Lihat Balasan', url('/'))
            ->line('Silakan login ke website perpustakaan untuk melihat balasan lengkap.')
            ->salutation('Salam, Perpustakaan UNIDA Gontor');
    }
}
