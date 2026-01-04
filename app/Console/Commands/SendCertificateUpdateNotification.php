<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PlagiarismCheck;
use App\Mail\CertificateUpdatedMail;
use Illuminate\Support\Facades\Mail;

class SendCertificateUpdateNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificate:send-update-notification {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to members about certificate updates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔍 Fetching plagiarism checks with certificates...');
        
        $checks = PlagiarismCheck::whereNotNull('certificate_number')
            ->with('member')
            ->get();
            
        $this->info("📊 Found {$checks->count()} certificates to notify");
        
        if ($checks->isEmpty()) {
            $this->warn('No certificates found to notify.');
            return;
        }
        
        $this->newLine();
        $bar = $this->output->createProgressBar($checks->count());
        $bar->start();
        
        $sent = 0;
        $failed = 0;
        
        foreach ($checks as $check) {
            try {
                if ($dryRun) {
                    $this->line("\n📧 Would send to: {$check->member->name} ({$check->member->email}) - {$check->certificate_number}");
                } else {
                    Mail::to($check->member->email)->send(new CertificateUpdatedMail($check));
                    $sent++;
                }
                
                $bar->advance();
                
            } catch (\Exception $e) {
                $failed++;
                $this->error("\n❌ Failed to send to {$check->member->email}: " . $e->getMessage());
                $bar->advance();
            }
        }
        
        $bar->finish();
        $this->newLine(2);
        
        if ($dryRun) {
            $this->info("🔍 Dry run completed. Would send {$checks->count()} emails.");
        } else {
            $this->info("✅ Email notifications sent successfully: {$sent}");
            if ($failed > 0) {
                $this->warn("⚠️  Failed to send: {$failed}");
            }
        }
        
        $this->newLine();
        $this->comment('💡 Tip: Use --dry-run to preview emails before sending');
    }
}
