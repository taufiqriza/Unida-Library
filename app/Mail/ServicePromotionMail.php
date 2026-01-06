<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServicePromotionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $websiteUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📚 Informasi Layanan Terbaru - Sistem Unggah Tugas Akhir & Cek Plagiasi Perpustakaan UNIDA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-promotion',
        );
    }
}
