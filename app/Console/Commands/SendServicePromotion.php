<?php

namespace App\Console\Commands;

use App\Mail\ServicePromotionMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendServicePromotion extends Command
{
    protected $signature = 'email:send-promotion {--email= : Kirim ke email tertentu} {--preview : Preview tanpa kirim}';
    protected $description = 'Kirim email promosi layanan ke fakultas dan BAAK';

    private array $recipients = [
        ['email' => 'baak@unida.gontor.ac.id', 'name' => 'BAAK UNIDA Gontor'],
        ['email' => 'ushuluddin@unida.gontor.ac.id', 'name' => 'Fakultas Ushuluddin'],
        ['email' => 'tarbiyah@unida.gontor.ac.id', 'name' => 'Fakultas Tarbiyah'],
        ['email' => 'syariah@unida.gontor.ac.id', 'name' => 'Fakultas Syariah'],
        ['email' => 'fem@unida.gontor.ac.id', 'name' => 'Fakultas Ekonomi & Manajemen'],
        ['email' => 'humaniora@unida.gontor.ac.id', 'name' => 'Fakultas Humaniora'],
        ['email' => 'saintek@unida.gontor.ac.id', 'name' => 'Fakultas Sains & Teknologi'],
        ['email' => 'fk@unida.gontor.ac.id', 'name' => 'Fakultas Kedokteran'],
        ['email' => 'pascasarjana@unida.gontor.ac.id', 'name' => 'Pascasarjana'],
    ];

    public function handle(): int
    {
        $websiteUrl = config('app.url');
        $targetEmail = $this->option('email');
        $isPreview = $this->option('preview');

        $recipients = $targetEmail 
            ? collect($this->recipients)->where('email', $targetEmail)->values()->all()
            : $this->recipients;

        if (empty($recipients)) {
            $this->error("Email {$targetEmail} tidak ditemukan dalam daftar.");
            return 1;
        }

        if ($isPreview) {
            $this->info("=== PREVIEW MODE ===\n");
            foreach ($recipients as $r) {
                $this->line("To: {$r['email']}");
                $this->line("Name: {$r['name']}\n");
            }
            return 0;
        }

        if (!$this->confirm('Kirim email ke ' . count($recipients) . ' penerima?')) {
            return 0;
        }

        $bar = $this->output->createProgressBar(count($recipients));
        $bar->start();

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient['email'])->send(
                    new ServicePromotionMail($recipient['name'], $websiteUrl)
                );
                $this->newLine();
                $this->info("✓ Terkirim: {$recipient['email']}");
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("✗ Gagal: {$recipient['email']} - {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Selesai!');

        return 0;
    }
}
