<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestAllEmails extends Command
{
    protected $signature = 'email:test-all {email}';
    protected $description = 'Test all email templates';

    public function handle()
    {
        $email = $this->argument('email');
        $this->info("Sending test emails to: {$email}");

        // 1. OTP Email
        $this->info('1. Sending OTP email...');
        try {
            Mail::send('emails.otp', [
                'name' => 'Test User',
                'otp' => '847291',
            ], function ($m) use ($email) {
                $m->to($email)->subject('🔐 Kode Verifikasi - UNIDA Library');
            });
            $this->info('   ✓ OTP email sent');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        sleep(2);

        // 2. Welcome Email
        $this->info('2. Sending Welcome email...');
        try {
            Mail::send('emails.welcome', [
                'name' => 'Test User',
                'loginUrl' => url('/login'),
            ], function ($m) use ($email) {
                $m->to($email)->subject('👋 Selamat Datang di UNIDA Library');
            });
            $this->info('   ✓ Welcome email sent');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        sleep(2);

        // 3. Publication Approved
        $this->info('3. Sending Publication Approved email...');
        try {
            Mail::send('emails.publication-approved', [
                'author' => 'Test User',
                'title' => 'Implementasi Sistem Informasi Perpustakaan Berbasis Web',
                'type' => 'Skripsi',
                'year' => '2025',
                'nim' => '2021001234',
                'portalUrl' => url('/member'),
            ], function ($m) use ($email) {
                $m->to($email)->subject('🎉 Karya Ilmiah Anda Telah Dipublikasikan - UNIDA Library');
            });
            $this->info('   ✓ Publication Approved email sent');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        sleep(2);

        // 4. Loan Reminder
        $this->info('4. Sending Loan Reminder email...');
        try {
            Mail::send('emails.loan-reminder', [
                'name' => 'Test User',
                'bookTitle' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'bookAuthor' => 'Robert C. Martin',
                'dueDate' => now()->addDays(3)->format('d F Y'),
                'portalUrl' => url('/member'),
            ], function ($m) use ($email) {
                $m->to($email)->subject('📖 Pengingat Pengembalian Buku - UNIDA Library');
            });
            $this->info('   ✓ Loan Reminder email sent');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        sleep(2);

        // 5. Loan Overdue
        $this->info('5. Sending Loan Overdue email...');
        try {
            Mail::send('emails.loan-overdue', [
                'name' => 'Test User',
                'bookTitle' => 'Design Patterns: Elements of Reusable Object-Oriented Software',
                'bookAuthor' => 'Gang of Four',
                'dueDate' => now()->subDays(5)->format('d F Y'),
                'daysOverdue' => 5,
                'fine' => 5000,
                'portalUrl' => url('/member'),
            ], function ($m) use ($email) {
                $m->to($email)->subject('⚠️ Buku Terlambat Dikembalikan - UNIDA Library');
            });
            $this->info('   ✓ Loan Overdue email sent');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        sleep(2);

        // 6. Password Reset
        $this->info('6. Sending Password Reset email...');
        try {
            Mail::send('emails.password-reset', [
                'name' => 'Test User',
                'code' => '582947',
            ], function ($m) use ($email) {
                $m->to($email)->subject('🔑 Reset Password - UNIDA Library');
            });
            $this->info('   ✓ Password Reset email sent');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('✅ All test emails sent! Check your inbox.');
    }
}
