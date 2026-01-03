<?php

namespace App\Services\Circulation;

use App\Models\{Loan, Member, MemberNotification, Reservation, FinePayment};
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendReservationCreated(Reservation $reservation): void
    {
        $this->createNotification(
            $reservation->member_id,
            'reservation',
            'Reservasi Berhasil',
            "Buku \"{$reservation->book->title}\" berhasil direservasi. Posisi antrian: {$reservation->queue_position}",
            ['reservation_id' => $reservation->id]
        );
    }

    public function sendReservationReady(Reservation $reservation): void
    {
        $deadline = $reservation->pickup_deadline->format('d M Y H:i');
        
        $this->createNotification(
            $reservation->member_id,
            'reservation',
            'Buku Siap Diambil!',
            "Buku \"{$reservation->book->title}\" siap diambil. Batas pengambilan: {$deadline}",
            ['reservation_id' => $reservation->id, 'urgent' => true]
        );

        $reservation->update(['notified_at' => now()]);

        // Send email
        $this->sendEmail($reservation->member, 'Buku Reservasi Siap Diambil', 
            "Buku \"{$reservation->book->title}\" sudah tersedia dan siap diambil.\n\nBatas pengambilan: {$deadline}\n\nSilakan kunjungi perpustakaan untuk mengambil buku Anda.");
    }

    public function sendRenewalSuccess(Loan $loan): void
    {
        $this->createNotification(
            $loan->member_id,
            'loan',
            'Perpanjangan Berhasil',
            "Buku \"{$loan->item->book->title}\" berhasil diperpanjang. Jatuh tempo baru: {$loan->due_date->format('d M Y')}",
            ['loan_id' => $loan->id]
        );
    }

    public function sendDueDateReminder(Loan $loan, int $daysLeft): void
    {
        $urgency = $daysLeft <= 1 ? 'urgent' : 'normal';
        $title = $daysLeft === 0 ? 'Jatuh Tempo Hari Ini!' : "Jatuh Tempo {$daysLeft} Hari Lagi";
        
        $this->createNotification(
            $loan->member_id,
            'loan',
            $title,
            "Buku \"{$loan->item->book->title}\" jatuh tempo pada {$loan->due_date->format('d M Y')}. Segera kembalikan atau perpanjang.",
            ['loan_id' => $loan->id, 'urgent' => $daysLeft <= 1]
        );

        if ($daysLeft <= 1) {
            $this->sendEmail($loan->member, $title,
                "Buku \"{$loan->item->book->title}\" akan jatuh tempo pada {$loan->due_date->format('d M Y')}.\n\nSilakan kembalikan atau perpanjang melalui dashboard member.");
        }
    }

    public function sendOverdueNotice(Loan $loan, int $daysOverdue): void
    {
        $fine = $daysOverdue * 500; // Rp 500/hari
        
        $this->createNotification(
            $loan->member_id,
            'fine',
            'Buku Terlambat!',
            "Buku \"{$loan->item->book->title}\" terlambat {$daysOverdue} hari. Denda: Rp " . number_format($fine),
            ['loan_id' => $loan->id, 'urgent' => true]
        );
    }

    public function sendPaymentSuccess(FinePayment $payment): void
    {
        $this->createNotification(
            $payment->member_id,
            'payment',
            'Pembayaran Berhasil',
            "Pembayaran denda sebesar Rp " . number_format($payment->amount) . " berhasil. Kode: {$payment->payment_code}",
            ['payment_id' => $payment->id]
        );
    }

    protected function createNotification(int $memberId, string $type, string $title, string $message, array $data = []): void
    {
        MemberNotification::create([
            'member_id' => $memberId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function sendEmail(Member $member, string $subject, string $body): void
    {
        if (!$member->email) return;

        try {
            Mail::raw($body, function ($message) use ($member, $subject) {
                $message->to($member->email, $member->name)
                    ->subject("[Perpustakaan UNIDA] {$subject}");
            });
        } catch (\Exception $e) {
            \Log::warning("Failed to send email to {$member->email}: " . $e->getMessage());
        }
    }
}
