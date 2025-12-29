<?php

namespace App\Notifications;

use App\Models\ThesisSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThesisStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ThesisSubmission $submission, public string $action) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $status = match($this->action) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision' => 'Perlu Revisi',
            default => $this->action,
        };

        $message = (new MailMessage)
            ->subject("Tugas Akhir: {$status} - Perpustakaan UNIDA")
            ->greeting("Assalamu'alaikum {$notifiable->name},");

        if ($this->action === 'approved') {
            $message->line("Selamat! Tugas akhir Anda telah **disetujui**.")
                ->line("**Judul:** {$this->submission->title}")
                ->action('Lihat Detail', url('/member/submissions'));
        } elseif ($this->action === 'rejected') {
            $message->line("Mohon maaf, tugas akhir Anda **ditolak**.")
                ->line("**Judul:** {$this->submission->title}")
                ->line("**Alasan:** " . ($this->submission->rejection_reason ?: '-'))
                ->action('Ajukan Ulang', url('/member/submit-thesis'));
        } else {
            $message->line("Tugas akhir Anda memerlukan **revisi**.")
                ->line("**Judul:** {$this->submission->title}")
                ->line("**Catatan:** " . ($this->submission->review_notes ?: '-'))
                ->action('Edit Submission', url("/member/submit-thesis/{$this->submission->id}"));
        }

        return $message->line('Terima kasih telah menggunakan layanan Perpustakaan UNIDA Gontor.');
    }
}
