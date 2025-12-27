<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'mail:test {email}';
    protected $description = 'Test email configuration';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            // Force use Resend
            config(['mail.default' => 'resend']);
            
            Mail::raw('Test email dari Perpustakaan UNIDA Gontor. Jika Anda menerima email ini, konfigurasi email berhasil.', function ($m) use ($email) {
                $m->to($email)->subject('Test Email - Perpustakaan UNIDA');
            });
            
            $this->info("Email berhasil dikirim ke {$email}");
        } catch (\Exception $e) {
            $this->error("Gagal mengirim email: " . $e->getMessage());
        }
    }
}
