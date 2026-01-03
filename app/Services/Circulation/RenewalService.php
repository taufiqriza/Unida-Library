<?php

namespace App\Services\Circulation;

use App\Models\{Loan, LoanRenewal, Member, Reservation};
use Illuminate\Support\Facades\DB;

class RenewalService
{
    public function __construct(protected NotificationService $notificationService) {}

    public function canRenew(Loan $loan): array
    {
        if ($loan->is_returned) {
            return ['can' => false, 'reason' => 'Buku sudah dikembalikan'];
        }

        $maxRenewals = $loan->max_renewals ?? $loan->member->memberType->max_renewals ?? 2;
        if ($loan->renewal_count >= $maxRenewals) {
            return ['can' => false, 'reason' => "Batas perpanjangan tercapai ({$maxRenewals}x)"];
        }

        if ($loan->due_date < now()) {
            return ['can' => false, 'reason' => 'Tidak dapat memperpanjang, buku sudah terlambat'];
        }

        // Check if book has reservation
        $hasReservation = Reservation::where('book_id', $loan->item->book_id)
            ->where('branch_id', $loan->branch_id)
            ->active()
            ->exists();

        if ($hasReservation) {
            return ['can' => false, 'reason' => 'Buku sedang direservasi member lain'];
        }

        return ['can' => true, 'reason' => null];
    }

    public function renew(Loan $loan, string $source = 'online', ?int $processedBy = null): array
    {
        $check = $this->canRenew($loan);
        if (!$check['can']) {
            return ['success' => false, 'message' => $check['reason']];
        }

        $renewalPeriod = $loan->member->memberType->loan_period ?? 7;
        $oldDueDate = $loan->due_date;
        $newDueDate = now()->addDays($renewalPeriod);

        DB::transaction(function () use ($loan, $oldDueDate, $newDueDate, $source, $processedBy) {
            $loan->increment('renewal_count');
            $loan->update(['due_date' => $newDueDate]);

            LoanRenewal::create([
                'loan_id' => $loan->id,
                'member_id' => $loan->member_id,
                'old_due_date' => $oldDueDate,
                'new_due_date' => $newDueDate,
                'renewal_number' => $loan->renewal_count,
                'source' => $source,
                'processed_by' => $processedBy,
            ]);
        });

        $this->notificationService->sendRenewalSuccess($loan);

        return [
            'success' => true,
            'message' => 'Perpanjangan berhasil!',
            'new_due_date' => $newDueDate->format('d M Y'),
            'renewals_left' => ($loan->max_renewals ?? 2) - $loan->renewal_count,
        ];
    }

    public function getRenewalHistory(Loan $loan)
    {
        return LoanRenewal::where('loan_id', $loan->id)->orderBy('created_at', 'desc')->get();
    }
}
