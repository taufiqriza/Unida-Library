<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SupportReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $staffName,
        public string $messagePreview,
        public ?string $topic = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $topicLabels = [
            'unggah' => 'Unggah Mandiri',
            'plagiasi' => 'Cek Plagiasi', 
            'bebas' => 'Bebas Pustaka',
            'pinjam' => 'Peminjaman',
            'lainnya' => 'Lainnya',
        ];
        
        $topicLabel = $topicLabels[$this->topic] ?? 'Support';
        
        return (new \Illuminate\Mail\Mailable)
            ->to($notifiable->email)
            ->subject("Balasan Support: {$topicLabel} - Perpustakaan UNIDA")
            ->view('emails.support-reply', [
                'name' => $notifiable->name,
                'staffName' => $this->staffName,
                'messagePreview' => $this->messagePreview,
                'topic' => $topicLabel,
                'chatUrl' => url('/member/support'),
                'subject' => "Balasan Support",
            ]);
    }
}
