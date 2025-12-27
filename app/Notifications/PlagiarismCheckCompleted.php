<?php

namespace App\Notifications;

use App\Models\PlagiarismCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlagiarismCheckCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PlagiarismCheck $check) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $status = $this->check->status === 'completed' ? 'Selesai' : 'Gagal';
        $score = $this->check->similarity_score;
        
        $message = (new MailMessage)
            ->subject("Hasil Cek Plagiarisme - {$status}")
            ->greeting("Assalamu'alaikum {$notifiable->name},");

        if ($this->check->status === 'completed') {
            $message->line("Pengecekan plagiarisme untuk dokumen Anda telah selesai.")
                ->line("**Dokumen:** {$this->check->document_title}")
                ->line("**Tingkat Kemiripan:** {$score}%")
                ->line($this->getScoreMessage($score))
                ->action('Lihat Hasil', url('/member/plagiarism-check/' . $this->check->id));
        } else {
            $message->line("Pengecekan plagiarisme untuk dokumen Anda gagal.")
                ->line("**Dokumen:** {$this->check->document_title}")
                ->line("**Alasan:** {$this->check->error_message}")
                ->line("Silakan coba lagi atau hubungi pustakawan.");
        }

        return $message->salutation("Wassalamu'alaikum,\nPerpustakaan UNIDA Gontor");
    }

    protected function getScoreMessage(float $score): string
    {
        if ($score <= 15) {
            return "✅ Dokumen Anda memiliki tingkat kemiripan rendah. Bagus!";
        } elseif ($score <= 25) {
            return "⚠️ Dokumen Anda memiliki tingkat kemiripan sedang. Mohon periksa kembali.";
        }
        return "❌ Dokumen Anda memiliki tingkat kemiripan tinggi. Perlu revisi.";
    }
}
