<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Member;
use App\Models\PlagiarismCheck;
use App\Models\ThesisSubmission;
use App\Models\ClearanceLetter;

class MemberNotificationService
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Loan due reminder (3 days before)
     */
    public function sendLoanDueReminder(Loan $loan): bool
    {
        if (!$loan->member) return false;

        $bookTitle = $loan->item?->book?->title ?? 'Buku';
        $dueDate = $loan->due_date->translatedFormat('d F Y');

        return $this->firebase->sendToMember(
            $loan->member,
            '📚 Pengingat Jatuh Tempo',
            "Buku \"{$bookTitle}\" akan jatuh tempo pada {$dueDate}",
            [
                'loan_id' => (string) $loan->id,
                'book_id' => (string) $loan->item?->book_id,
                'action' => 'view_loan',
            ],
            'loan_due_reminder'
        );
    }

    /**
     * Loan due today
     */
    public function sendLoanDueToday(Loan $loan): bool
    {
        if (!$loan->member) return false;

        $bookTitle = $loan->item?->book?->title ?? 'Buku';

        return $this->firebase->sendToMember(
            $loan->member,
            '⏰ Jatuh Tempo Hari Ini',
            "Buku \"{$bookTitle}\" jatuh tempo hari ini. Segera kembalikan!",
            [
                'loan_id' => (string) $loan->id,
                'book_id' => (string) $loan->item?->book_id,
                'action' => 'view_loan',
            ],
            'loan_due_today'
        );
    }

    /**
     * Loan overdue
     */
    public function sendLoanOverdue(Loan $loan): bool
    {
        if (!$loan->member) return false;

        $bookTitle = $loan->item?->book?->title ?? 'Buku';
        $daysOverdue = $loan->days_overdue;

        return $this->firebase->sendToMember(
            $loan->member,
            '🚨 Peminjaman Terlambat',
            "Buku \"{$bookTitle}\" sudah terlambat {$daysOverdue} hari. Denda akan dikenakan.",
            [
                'loan_id' => (string) $loan->id,
                'book_id' => (string) $loan->item?->book_id,
                'days_overdue' => (string) $daysOverdue,
                'action' => 'view_loan',
            ],
            'loan_overdue'
        );
    }

    /**
     * Plagiarism check completed
     */
    public function sendPlagiarismComplete(PlagiarismCheck $check): bool
    {
        if (!$check->member) return false;

        $score = number_format($check->similarity_score, 1);
        $status = $check->isPassed() ? '✅ LOLOS' : '⚠️ PERLU REVISI';

        return $this->firebase->sendToMember(
            $check->member,
            '📋 Hasil Cek Plagiasi',
            "{$status} - Similarity: {$score}%",
            [
                'check_id' => (string) $check->id,
                'score' => $score,
                'passed' => $check->isPassed() ? '1' : '0',
                'action' => 'view_plagiarism',
            ],
            'plagiarism_complete'
        );
    }

    /**
     * Thesis submission status changed
     */
    public function sendSubmissionStatus(ThesisSubmission $submission): bool
    {
        if (!$submission->member) return false;

        $statusMessages = [
            'under_review' => ['🔍 Sedang Direview', 'Pengajuan karya ilmiah Anda sedang direview'],
            'revision_required' => ['📝 Perlu Revisi', 'Pengajuan karya ilmiah Anda memerlukan revisi'],
            'approved' => ['✅ Disetujui', 'Pengajuan karya ilmiah Anda telah disetujui'],
            'rejected' => ['❌ Ditolak', 'Pengajuan karya ilmiah Anda ditolak'],
            'published' => ['🎉 Dipublikasikan', 'Karya ilmiah Anda telah dipublikasikan di E-Thesis'],
        ];

        $message = $statusMessages[$submission->status] ?? ['📋 Status Update', 'Status pengajuan Anda telah diperbarui'];

        return $this->firebase->sendToMember(
            $submission->member,
            $message[0],
            $message[1],
            [
                'submission_id' => (string) $submission->id,
                'status' => $submission->status,
                'action' => 'view_submission',
            ],
            'submission_status'
        );
    }

    /**
     * Clearance letter approved
     */
    public function sendClearanceApproved(ClearanceLetter $letter): bool
    {
        if (!$letter->member) return false;

        return $this->firebase->sendToMember(
            $letter->member,
            '🎓 Surat Bebas Pustaka Disetujui',
            "Surat bebas pustaka Anda telah disetujui. Silakan download di aplikasi.",
            [
                'letter_id' => (string) $letter->id,
                'letter_number' => $letter->letter_number,
                'action' => 'view_clearance',
            ],
            'clearance_approved'
        );
    }

    /**
     * General announcement to all members
     */
    public function sendAnnouncement(string $title, string $body, array $data = []): int
    {
        $members = Member::whereHas('devices')->get();
        $sent = 0;

        foreach ($members as $member) {
            if ($this->firebase->sendToMember($member, $title, $body, $data, 'announcement')) {
                $sent++;
            }
        }

        return $sent;
    }
}
