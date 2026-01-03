<?php

namespace App\Console\Commands;

use App\Models\{Loan, Reservation};
use App\Services\Circulation\{NotificationService, ReservationService};
use Illuminate\Console\Command;

class SendLoanReminders extends Command
{
    protected $signature = 'loans:send-reminders';
    protected $description = 'Send due date reminders and overdue notices';

    public function handle(NotificationService $notificationService): int
    {
        $this->info('Sending loan reminders...');

        // Due date reminders (3 days, 1 day, same day)
        foreach ([3, 1, 0] as $days) {
            $loans = Loan::with('member', 'item.book')
                ->where('is_returned', false)
                ->whereDate('due_date', now()->addDays($days))
                ->get();

            foreach ($loans as $loan) {
                $notificationService->sendDueDateReminder($loan, $days);
            }

            $this->info("Sent {$loans->count()} reminders for {$days} days left");
        }

        // Overdue notices (1, 3, 7 days overdue)
        foreach ([1, 3, 7] as $days) {
            $loans = Loan::with('member', 'item.book')
                ->where('is_returned', false)
                ->whereDate('due_date', now()->subDays($days))
                ->get();

            foreach ($loans as $loan) {
                $notificationService->sendOverdueNotice($loan, $days);
            }

            $this->info("Sent {$loans->count()} overdue notices for {$days} days overdue");
        }

        return Command::SUCCESS;
    }
}
