<?php

namespace App\Notifications;

use App\Models\PlagiarismCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class PlagiarismCertificateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PlagiarismCheck $check) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Mail\Mailable)
            ->to($notifiable->email)
            ->subject("Sertifikat Bebas Plagiasi Terbit - Perpustakaan UNIDA")
            ->view('emails.plagiarism-certificate-issued', [
                'name' => $notifiable->name,
                'documentTitle' => $this->check->document_title,
                'certificateNumber' => $this->check->certificate_number,
                'similarityScore' => $this->check->similarity_score,
                'certificateUrl' => url("/member/plagiarism/{$this->check->id}/certificate"),
                'subject' => "Sertifikat Bebas Plagiasi Terbit",
            ]);
    }
}
