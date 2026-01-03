<?php

namespace App\Services\Circulation;

use App\Models\{Book, Item, Member, Reservation, Loan};
use App\Services\Circulation\NotificationService;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function __construct(protected NotificationService $notificationService) {}

    public function reserve(Member $member, Book $book, ?int $branchId = null): array
    {
        // Validations
        if ($member->isExpired()) {
            return ['success' => false, 'message' => 'Keanggotaan sudah kadaluarsa'];
        }

        $activeReservation = Reservation::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->active()
            ->exists();

        if ($activeReservation) {
            return ['success' => false, 'message' => 'Anda sudah memiliki reservasi aktif untuk buku ini'];
        }

        $activeLoan = Loan::where('member_id', $member->id)
            ->whereHas('item', fn($q) => $q->where('book_id', $book->id))
            ->where('is_returned', false)
            ->exists();

        if ($activeLoan) {
            return ['success' => false, 'message' => 'Anda sedang meminjam buku ini'];
        }

        $maxReservations = $member->memberType->reservation_limit ?? 3;
        $currentReservations = Reservation::where('member_id', $member->id)->active()->count();

        if ($currentReservations >= $maxReservations) {
            return ['success' => false, 'message' => "Batas reservasi tercapai (maksimal {$maxReservations})"];
        }

        // If no branch specified, get from first borrowed item or member's branch
        if (!$branchId) {
            $borrowedItem = Item::where('book_id', $book->id)
                ->whereHas('loans', fn($q) => $q->where('is_returned', false))
                ->first();
            $branchId = $borrowedItem?->branch_id ?? $member->branch_id ?? 1;
        }

        // Check if book available - no need to reserve
        $availableItem = Item::where('book_id', $book->id)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDoesntHave('loans', fn($q) => $q->where('is_returned', false))
            ->first();

        if ($availableItem) {
            return ['success' => false, 'message' => 'Buku tersedia, silakan pinjam langsung'];
        }

        // Calculate queue position
        $queuePosition = Reservation::where('book_id', $book->id)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->pending()
            ->count() + 1;

        $reservation = Reservation::create([
            'branch_id' => $branchId,
            'member_id' => $member->id,
            'book_id' => $book->id,
            'queue_position' => $queuePosition,
        ]);

        $this->notificationService->sendReservationCreated($reservation);

        return [
            'success' => true,
            'message' => "Reservasi berhasil! Posisi antrian: {$queuePosition}",
            'reservation' => $reservation,
        ];
    }

    public function cancel(Reservation $reservation, string $reason = 'Dibatalkan oleh member'): array
    {
        if (!$reservation->isPending() && !$reservation->isReady()) {
            return ['success' => false, 'message' => 'Reservasi tidak dapat dibatalkan'];
        }

        $reservation->cancel($reason);
        $this->recalculateQueue($reservation->book_id, $reservation->branch_id);

        return ['success' => true, 'message' => 'Reservasi dibatalkan'];
    }

    public function processReturnedItem(Item $item): void
    {
        $nextReservation = Reservation::where('book_id', $item->book_id)
            ->where('branch_id', $item->branch_id)
            ->pending()
            ->orderBy('queue_position')
            ->first();

        if ($nextReservation) {
            $nextReservation->markAsReady($item);
            $this->notificationService->sendReservationReady($nextReservation);
        }
    }

    public function expireOverdueReservations(): int
    {
        $expired = Reservation::where('status', 'ready')
            ->where('pickup_deadline', '<', now())
            ->get();

        foreach ($expired as $reservation) {
            $reservation->expire();
            $this->recalculateQueue($reservation->book_id, $reservation->branch_id);
            
            // Make item available for next in queue
            if ($reservation->item_id) {
                $this->processReturnedItem($reservation->item);
            }
        }

        return $expired->count();
    }

    protected function recalculateQueue(int $bookId, int $branchId): void
    {
        $reservations = Reservation::where('book_id', $bookId)
            ->where('branch_id', $branchId)
            ->pending()
            ->orderBy('created_at')
            ->get();

        foreach ($reservations as $index => $reservation) {
            $reservation->update(['queue_position' => $index + 1]);
        }
    }
}
