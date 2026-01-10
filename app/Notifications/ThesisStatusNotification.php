<?php

namespace App\Notifications;

use App\Models\ThesisSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class ThesisStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ThesisSubmission $submission, public string $action) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $status = match($this->action) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision' => 'Perlu Revisi',
            default => $this->action,
        };

        $template = match($this->action) {
            'approved' => 'emails.thesis-approved',
            'rejected' => 'emails.thesis-rejected',
            'revision' => 'emails.thesis-revision',
            default => 'emails.thesis-revision',
        };

        $data = [
            'name' => $notifiable->name,
            'title' => $this->submission->title,
            'nim' => $notifiable->member_id ?? null,
            'subject' => "Tugas Akhir: {$status}",
        ];

        if ($this->action === 'approved') {
            $data['detailUrl'] = url('/member/submissions');
        } elseif ($this->action === 'rejected') {
            $data['rejectionReason'] = $this->submission->rejection_reason ?: '-';
            $data['submitUrl'] = url('/member/submit-thesis');
        } else {
            $data['reviewNotes'] = $this->submission->review_notes ?: '-';
            $data['editUrl'] = url("/member/submit-thesis/{$this->submission->id}");
        }

        return (new \Illuminate\Mail\Mailable)
            ->to($notifiable->email)
            ->subject("Tugas Akhir: {$status} - Perpustakaan UNIDA")
            ->view($template, $data);
    }
}
