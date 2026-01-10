<?php

namespace App\Notifications;

use App\Models\ClearanceLetter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class ClearanceLetterNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ClearanceLetter $letter) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Mail\Mailable)
            ->to($notifiable->email)
            ->subject("Surat Bebas Pustaka Terbit - Perpustakaan UNIDA")
            ->view('emails.clearance-letter-issued', [
                'name' => $notifiable->name,
                'letterNumber' => $this->letter->letter_number,
                'purpose' => $this->letter->purpose,
                'letterUrl' => url("/member/clearance-letter/{$this->letter->id}"),
                'subject' => "Surat Bebas Pustaka Terbit",
            ]);
    }
}
