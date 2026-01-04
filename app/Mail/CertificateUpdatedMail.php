<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PlagiarismCheck;

class CertificateUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $plagiarismCheck;

    /**
     * Create a new message instance.
     */
    public function __construct(PlagiarismCheck $plagiarismCheck)
    {
        $this->plagiarismCheck = $plagiarismCheck;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembaruan Sertifikat Originalitas - ' . $this->plagiarismCheck->certificate_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate-updated',
            with: [
                'memberName' => $this->plagiarismCheck->member->name,
                'documentTitle' => $this->plagiarismCheck->document_title,
                'certificateNumber' => $this->plagiarismCheck->certificate_number,
                'similarityScore' => number_format($this->plagiarismCheck->similarity_score, 0),
                'dashboardUrl' => route('member.dashboard') . '#plagiarism-checks',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
