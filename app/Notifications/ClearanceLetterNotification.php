<?php

namespace App\Notifications;

use App\Models\ClearanceLetter;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClearanceLetterNotification extends Notification
{
    public function __construct(public ClearanceLetter $letter) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Surat Bebas Pustaka Terbit - Perpustakaan UNIDA")
            ->greeting("Assalamu'alaikum {$notifiable->name},")
            ->line("Surat Bebas Pustaka Anda telah **diterbitkan**.")
            ->line("**Nomor Surat:** {$this->letter->letter_number}")
            ->line("**Keperluan:** {$this->letter->purpose}")
            ->action('Unduh Surat', url("/member/clearance-letter/{$this->letter->id}"))
            ->line('Terima kasih telah menggunakan layanan Perpustakaan UNIDA Gontor.');
    }
}
