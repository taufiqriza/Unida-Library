<?php

namespace App\Console\Commands;

use App\Services\Circulation\ReservationService;
use Illuminate\Console\Command;

class ExpireReservations extends Command
{
    protected $signature = 'reservations:expire';
    protected $description = 'Expire overdue reservations';

    public function handle(ReservationService $reservationService): int
    {
        $count = $reservationService->expireOverdueReservations();
        $this->info("Expired {$count} reservations");
        return Command::SUCCESS;
    }
}
