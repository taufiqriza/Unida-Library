<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Services\MemberNotificationService;
use Illuminate\Console\Command;

class SendLoanReminders extends Command
{
    protected $signature = 'loans:send-reminders';
    protected $description = 'Send push notifications for loan due dates';

    public function handle(MemberNotificationService $notificationService): int
    {
        $this->info('Sending loan reminders...');

        // 3 days before due
        $dueSoon = Loan::with(['member', 'item.book'])
            ->where('is_returned', false)
            ->whereDate('due_date', now()->addDays(3)->toDateString())
            ->get();

        $this->info("Found {$dueSoon->count()} loans due in 3 days");
        foreach ($dueSoon as $loan) {
            $notificationService->sendLoanDueReminder($loan);
        }

        // Due today
        $dueToday = Loan::with(['member', 'item.book'])
            ->where('is_returned', false)
            ->whereDate('due_date', now()->toDateString())
            ->get();

        $this->info("Found {$dueToday->count()} loans due today");
        foreach ($dueToday as $loan) {
            $notificationService->sendLoanDueToday($loan);
        }

        // Overdue (1 day)
        $overdue = Loan::with(['member', 'item.book'])
            ->where('is_returned', false)
            ->whereDate('due_date', now()->subDay()->toDateString())
            ->get();

        $this->info("Found {$overdue->count()} loans overdue 1 day");
        foreach ($overdue as $loan) {
            $notificationService->sendLoanOverdue($loan);
        }

        $this->info('Done!');
        return Command::SUCCESS;
    }
}
