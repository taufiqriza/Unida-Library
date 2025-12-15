<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AppSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Pengaturan Aplikasi';
    protected static ?string $navigationLabel = 'App Settings';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Perpustakaan';
    protected static ?int $navigationSort = 99;
    protected static string $view = 'filament.pages.app-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'app_name' => Setting::get('app_name', config('app.name')),
            'app_tagline' => Setting::get('app_tagline', ''),
            'app_logo' => Setting::get('app_logo'),
            'app_favicon' => Setting::get('app_favicon'),
            'contact_email' => Setting::get('contact_email', ''),
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_whatsapp' => Setting::get('contact_whatsapp', ''),
            'contact_address' => Setting::get('contact_address', ''),
            'social_instagram' => Setting::get('social_instagram', ''),
            'social_facebook' => Setting::get('social_facebook', ''),
            'social_twitter' => Setting::get('social_twitter', ''),
            'social_youtube' => Setting::get('social_youtube', ''),
            'google_enabled' => (bool) Setting::get('google_enabled', false),
            'google_client_id' => Setting::get('google_client_id', ''),
            'google_client_secret' => Setting::get('google_client_secret', ''),
            'google_allowed_domains' => Setting::get('google_allowed_domains', ''),
            // Plagiarism Settings
            'plagiarism_enabled' => (bool) Setting::get('plagiarism_enabled', true),
            'plagiarism_provider' => Setting::get('plagiarism_provider', 'internal'),
            'plagiarism_pass_threshold' => (float) Setting::get('plagiarism_pass_threshold', 25),
            'plagiarism_warning_threshold' => (float) Setting::get('plagiarism_warning_threshold', 15),
            'plagiarism_min_words' => (int) Setting::get('plagiarism_min_words', 100),
            'plagiarism_head_librarian' => Setting::get('plagiarism_head_librarian', ''),
            'plagiarism_max_file_size' => (int) Setting::get('plagiarism_max_file_size', 20),
            // iThenticate/TCA API Settings
            'ithenticate_integration_name' => Setting::get('ithenticate_integration_name', ''),
            'ithenticate_api_key' => Setting::get('ithenticate_api_key', ''),
            'ithenticate_api_secret' => Setting::get('ithenticate_api_secret', ''),
            'ithenticate_base_url' => Setting::get('ithenticate_base_url', 'https://api.turnitin.com'),
            // Integration Settings
            'repo_enabled' => (bool) Setting::get('repo_enabled', true),
            'repo_oai_url' => Setting::get('repo_oai_url', 'https://repo.unida.gontor.ac.id/cgi/oai2'),
            'repo_sync_schedule' => Setting::get('repo_sync_schedule', 'weekly'),
            'journal_enabled' => (bool) Setting::get('journal_enabled', true),
            'journal_ojs_url' => Setting::get('journal_ojs_url', 'https://ejournal.unida.gontor.ac.id'),
            'journal_sync_schedule' => Setting::get('journal_sync_schedule', 'daily'),
            'journal_scrape_schedule' => Setting::get('journal_scrape_schedule', 'weekly'),
            'kubuku_enabled' => (bool) Setting::get('kubuku_enabled', false),
            'kubuku_sync_schedule' => Setting::get('kubuku_sync_schedule', 'daily'),
            'kubuku_api_url' => Setting::get('kubuku_api_url', ''),
            'kubuku_api_key' => Setting::get('kubuku_api_key', ''),
            'kubuku_library_id' => Setting::get('kubuku_library_id', ''),
            // Open Library Settings
            'openlibrary_enabled' => (bool) Setting::get('openlibrary_enabled', true),
            'openlibrary_search_limit' => (int) Setting::get('openlibrary_search_limit', 10),
            // Email Settings
            'mail_mailer' => Setting::get('mail_mailer', config('mail.default', 'smtp')),
            'mail_host' => Setting::get('mail_host', config('mail.mailers.smtp.host', '')),
            'mail_port' => Setting::get('mail_port', config('mail.mailers.smtp.port', 587)),
            'mail_username' => Setting::get('mail_username', config('mail.mailers.smtp.username', '')),
            'mail_password' => Setting::get('mail_password', ''),
            'mail_encryption' => Setting::get('mail_encryption', config('mail.mailers.smtp.encryption', 'tls')),
            'mail_from_address' => Setting::get('mail_from_address', config('mail.from.address', '')),
            'mail_from_name' => Setting::get('mail_from_name', config('mail.from.name', 'UNIDA Library')),
            // Google Analytics Settings
            'ga_enabled' => (bool) Setting::get('ga_enabled', false),
            'ga_measurement_id' => Setting::get('ga_measurement_id', ''),
            'ga_property_id' => Setting::get('ga_property_id', ''),
            'ga_service_account_json' => Setting::get('ga_service_account_json', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')->tabs([
                    Forms\Components\Tabs\Tab::make('Umum')
                        ->icon('heroicon-o-building-library')
                        ->schema([
                            Forms\Components\Section::make('Identitas Perpustakaan')->schema([
                                Forms\Components\TextInput::make('app_name')
                                    ->label('Nama Perpustakaan')
                                    ->required(),
                                Forms\Components\TextInput::make('app_tagline')
                                    ->label('Tagline'),
                            ])->columns(2),
                            Forms\Components\Section::make('Logo & Favicon')->schema([
                                Forms\Components\FileUpload::make('app_logo')
                                    ->label('Logo')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                                Forms\Components\FileUpload::make('app_favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings'),
                            ])->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Kontak')
                        ->icon('heroicon-o-phone')
                        ->schema([
                            Forms\Components\Section::make('Informasi Kontak')->schema([
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Email')
                                    ->email(),
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Telepon'),
                                Forms\Components\TextInput::make('contact_whatsapp')
                                    ->label('WhatsApp')
                                    ->placeholder('6281234567890'),
                                Forms\Components\Textarea::make('contact_address')
                                    ->label('Alamat')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Social Media')
                        ->icon('heroicon-o-share')
                        ->schema([
                            Forms\Components\Section::make('Link Social Media')->schema([
                                Forms\Components\TextInput::make('social_instagram')
                                    ->label('Instagram')
                                    ->prefix('@'),
                                Forms\Components\TextInput::make('social_facebook')
                                    ->label('Facebook'),
                                Forms\Components\TextInput::make('social_twitter')
                                    ->label('Twitter/X')
                                    ->prefix('@'),
                                Forms\Components\TextInput::make('social_youtube')
                                    ->label('YouTube'),
                            ])->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Email')
                        ->icon('heroicon-o-envelope')
                        ->schema([
                            Forms\Components\Section::make('Konfigurasi SMTP')
                                ->description('Pengaturan untuk mengirim email (OTP verifikasi, notifikasi, dll).')
                                ->schema([
                                    Forms\Components\Select::make('mail_mailer')
                                        ->label('Mailer')
                                        ->options([
                                            'smtp' => 'SMTP',
                                            'sendmail' => 'Sendmail',
                                            'log' => 'Log (Testing)',
                                        ])
                                        ->default('smtp'),
                                    Forms\Components\TextInput::make('mail_host')
                                        ->label('SMTP Host')
                                        ->placeholder('smtp.gmail.com')
                                        ->helperText('Untuk Google Workspace: smtp.gmail.com'),
                                    Forms\Components\TextInput::make('mail_port')
                                        ->label('Port')
                                        ->numeric()
                                        ->default(587)
                                        ->helperText('587 untuk TLS, 465 untuk SSL'),
                                    Forms\Components\Select::make('mail_encryption')
                                        ->label('Enkripsi')
                                        ->options([
                                            'tls' => 'TLS',
                                            'ssl' => 'SSL',
                                            '' => 'None',
                                        ])
                                        ->default('tls'),
                                ])->columns(2),
                            Forms\Components\Section::make('Kredensial')
                                ->schema([
                                    Forms\Components\TextInput::make('mail_username')
                                        ->label('Username/Email')
                                        ->placeholder('library@unida.gontor.ac.id')
                                        ->helperText('Email yang digunakan untuk login SMTP'),
                                    Forms\Components\TextInput::make('mail_password')
                                        ->label('Password / App Password')
                                        ->password()
                                        ->revealable()
                                        ->helperText('Untuk Gmail: gunakan App Password (16 digit)'),
                                ])->columns(2),
                            Forms\Components\Section::make('Pengirim')
                                ->schema([
                                    Forms\Components\TextInput::make('mail_from_address')
                                        ->label('Email Pengirim')
                                        ->placeholder('library@unida.gontor.ac.id')
                                        ->helperText('Alamat email yang tampil sebagai pengirim'),
                                    Forms\Components\TextInput::make('mail_from_name')
                                        ->label('Nama Pengirim')
                                        ->placeholder('UNIDA Library')
                                        ->helperText('Nama yang tampil sebagai pengirim'),
                                ])->columns(2),
                            Forms\Components\Section::make('Test Email')
                                ->schema([
                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('test_email')
                                            ->label('Kirim Test Email')
                                            ->icon('heroicon-o-paper-airplane')
                                            ->color('success')
                                            ->requiresConfirmation()
                                            ->modalHeading('Test Kirim Email')
                                            ->modalDescription('Email test akan dikirim ke alamat email pengirim yang dikonfigurasi.')
                                            ->action(function ($get) {
                                                $this->testEmail($get);
                                            }),
                                    ]),
                                    Forms\Components\Placeholder::make('email_tips')
                                        ->label('Tips Menghindari Spam')
                                        ->content('1. Gunakan email domain sendiri (bukan @gmail.com)
2. Pastikan SPF, DKIM, DMARC sudah dikonfigurasi di DNS
3. Untuk Google Workspace, aktifkan "Less secure apps" atau gunakan App Password'),
                                ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Google OAuth')
                        ->icon('heroicon-o-key')
                        ->schema([
                            Forms\Components\Section::make('Google Login')
                                ->description('Aktifkan login dengan Google untuk anggota perpustakaan.')
                                ->schema([
                                    Forms\Components\Toggle::make('google_enabled')
                                        ->label('Aktifkan Google Login')
                                        ->helperText('Tampilkan tombol "Login dengan Google"'),
                                    Forms\Components\TextInput::make('google_client_id')
                                        ->label('Client ID')
                                        ->placeholder('xxxxx.apps.googleusercontent.com'),
                                    Forms\Components\TextInput::make('google_client_secret')
                                        ->label('Client Secret')
                                        ->password()
                                        ->revealable(),
                                ]),
                            Forms\Components\Section::make('Domain Email yang Diizinkan')
                                ->description('Hanya email dengan domain ini yang bisa login. Satu domain per baris.')
                                ->schema([
                                    Forms\Components\Textarea::make('google_allowed_domains')
                                        ->label('Domain yang Diizinkan')
                                        ->placeholder("@unida.gontor.ac.id\n@student.unida.gontor.ac.id\n@staff.unida.gontor.ac.id")
                                        ->rows(10)
                                        ->helperText('Contoh: @unida.gontor.ac.id'),
                                ]),
                            Forms\Components\Section::make('Panduan Setup')
                                ->schema([
                                    Forms\Components\Placeholder::make('redirect_uri')
                                        ->label('Redirect URI')
                                        ->content(fn () => url('/auth/google/callback')),
                                ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Plagiarism')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Forms\Components\Section::make('Pengaturan Cek Plagiasi')
                                ->description('Konfigurasi layanan pengecekan plagiarisme dokumen.')
                                ->schema([
                                    Forms\Components\Toggle::make('plagiarism_enabled')
                                        ->label('Aktifkan Layanan Cek Plagiasi')
                                        ->helperText('Tampilkan menu cek plagiasi di dashboard member'),
                                    Forms\Components\Select::make('plagiarism_provider')
                                        ->label('Provider')
                                        ->options([
                                            'internal' => 'Internal (Database E-Thesis)',
                                            'ithenticate' => 'Turnitin / iThenticate (TCA)',
                                        ])
                                        ->default('ithenticate')
                                        ->helperText('Turnitin dan iThenticate menggunakan API yang sama (TCA)'),
                                ])->columns(2),
                            Forms\Components\Section::make('Threshold Similarity')
                                ->description('Atur batas persentase similarity untuk hasil pengecekan.')
                                ->schema([
                                    Forms\Components\TextInput::make('plagiarism_pass_threshold')
                                        ->label('Batas Lolos (%)')
                                        ->numeric()
                                        ->default(25)
                                        ->minValue(1)
                                        ->maxValue(100)
                                        ->suffix('%')
                                        ->helperText('Dokumen dengan similarity ≤ nilai ini dinyatakan LOLOS'),
                                    Forms\Components\TextInput::make('plagiarism_warning_threshold')
                                        ->label('Batas Peringatan (%)')
                                        ->numeric()
                                        ->default(15)
                                        ->minValue(1)
                                        ->maxValue(100)
                                        ->suffix('%')
                                        ->helperText('Similarity di atas nilai ini akan diberi peringatan'),
                                    Forms\Components\TextInput::make('plagiarism_min_words')
                                        ->label('Minimal Kata')
                                        ->numeric()
                                        ->default(100)
                                        ->helperText('Jumlah kata minimal untuk bisa dicek'),
                                    Forms\Components\TextInput::make('plagiarism_max_file_size')
                                        ->label('Maks. Ukuran File')
                                        ->numeric()
                                        ->default(20)
                                        ->suffix('MB')
                                        ->helperText('Ukuran maksimal file yang bisa diupload'),
                                ])->columns(2),
                            Forms\Components\Section::make('Sertifikat')
                                ->schema([
                                    Forms\Components\TextInput::make('plagiarism_head_librarian')
                                        ->label('Nama Kepala Perpustakaan')
                                        ->placeholder('Dr. H. Ahmad Fulan, M.A.')
                                        ->helperText('Nama yang akan tercantum di sertifikat'),
                                ]),
                            Forms\Components\Section::make('Turnitin Core API (TCA)')
                                ->description('Konfigurasi API untuk Turnitin/iThenticate. Keduanya menggunakan API yang sama (TCA). Saat migrasi, cukup ganti Base URL dan API Key.')
                                ->schema([
                                    Forms\Components\TextInput::make('ithenticate_integration_name')
                                        ->label('Integration Name')
                                        ->placeholder('Library-Portal-API')
                                        ->helperText('Nama integrasi untuk header X-Turnitin-Integration-Name'),
                                    Forms\Components\TextInput::make('ithenticate_api_key')
                                        ->label('Key Name (Referensi)')
                                        ->placeholder('SYSTEM-LIBRARY')
                                        ->helperText('Nama key untuk referensi internal'),
                                    Forms\Components\TextInput::make('ithenticate_api_secret')
                                        ->label('API Secret Key')
                                        ->password()
                                        ->revealable()
                                        ->helperText('Secret key dari TCA Admin Console (Bearer token)'),
                                    Forms\Components\TextInput::make('ithenticate_base_url')
                                        ->label('API Base URL')
                                        ->default('https://unidagontor.turnitin.com')
                                        ->helperText('Format: https://[tenant-name].turnitin.com'),
                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('test_ithenticate')
                                            ->label('Test Koneksi API')
                                            ->icon('heroicon-o-signal')
                                            ->color('info')
                                            ->action(function ($get) {
                                                $baseUrl = rtrim($get('ithenticate_base_url') ?: 'https://unidagontor.turnitin.com', '/');
                                                $apiSecret = $get('ithenticate_api_secret');
                                                $integrationName = $get('ithenticate_integration_name') ?: 'Library-Portal-API';
                                                
                                                if (empty($apiSecret)) {
                                                    Notification::make()
                                                        ->title('API Secret Key harus diisi')
                                                        ->danger()
                                                        ->send();
                                                    return;
                                                }
                                                
                                                try {
                                                    // Test dengan endpoint EULA (sesuai provider)
                                                    $response = \Http::timeout(15)->withHeaders([
                                                        'Authorization' => 'Bearer ' . $apiSecret,
                                                        'X-Turnitin-Integration-Name' => $integrationName,
                                                        'X-Turnitin-Integration-Version' => '1.0.0',
                                                        'Content-Type' => 'application/json',
                                                    ])->get($baseUrl . '/api/v1/eula/latest');
                                                    
                                                    if ($response->successful()) {
                                                        $data = $response->json();
                                                        Notification::make()
                                                            ->title('Koneksi Berhasil!')
                                                            ->body('Turnitin TCA terhubung. EULA Version: ' . ($data['version'] ?? 'OK'))
                                                            ->success()
                                                            ->send();
                                                    } elseif ($response->status() === 401 || $response->status() === 403) {
                                                        Notification::make()
                                                            ->title('Kredensial Tidak Valid')
                                                            ->body('Server merespons, tapi Secret Key tidak valid.')
                                                            ->warning()
                                                            ->send();
                                                    } else {
                                                        Notification::make()
                                                            ->title('Koneksi Gagal')
                                                            ->body('Status: ' . $response->status())
                                                            ->danger()
                                                            ->send();
                                                    }
                                                } catch (\Exception $e) {
                                                    Notification::make()
                                                        ->title('Error Koneksi')
                                                        ->body('Tidak dapat terhubung ke ' . $baseUrl)
                                                        ->danger()
                                                        ->send();
                                                }
                                            }),
                                    ])->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Integrasi')
                        ->icon('heroicon-o-arrow-path')
                        ->schema([
                            Forms\Components\Section::make('Repository UNIDA (E-Thesis)')
                                ->description('Sinkronisasi E-Thesis dari repo.unida.gontor.ac.id via OAI-PMH.')
                                ->schema([
                                    Forms\Components\Toggle::make('repo_enabled')
                                        ->label('Aktifkan Sinkronisasi')
                                        ->helperText('Otomatis sync E-Thesis dari repository'),
                                    Forms\Components\TextInput::make('repo_oai_url')
                                        ->label('OAI-PMH URL')
                                        ->default('https://repo.unida.gontor.ac.id/cgi/oai2')
                                        ->helperText('Endpoint OAI-PMH repository'),
                                    Forms\Components\Select::make('repo_sync_schedule')
                                        ->label('Jadwal Sync')
                                        ->options([
                                            'daily' => 'Harian (setiap jam 02:00)',
                                            'weekly' => 'Mingguan (Sabtu jam 02:00)',
                                            'monthly' => 'Bulanan (tanggal 1)',
                                        ])
                                        ->default('weekly'),
                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('sync_repo')
                                            ->label('Sync Sekarang')
                                            ->icon('heroicon-o-arrow-path')
                                            ->color('success')
                                            ->action(function () {
                                                \Artisan::call('repo:sync');
                                                Notification::make()->title('Sync Repository dimulai')->success()->send();
                                            }),
                                    ]),
                                ])->columns(2),

                            Forms\Components\Section::make('Journal OJS (Artikel Jurnal)')
                                ->description('Sinkronisasi artikel jurnal dari ejournal.unida.gontor.ac.id. Tersedia 2 metode: Sync (via feed, cepat) dan Scrape (dari archive, lengkap).')
                                ->schema([
                                    Forms\Components\Toggle::make('journal_enabled')
                                        ->label('Aktifkan Sinkronisasi')
                                        ->helperText('Otomatis sync artikel dari OJS'),
                                    Forms\Components\TextInput::make('journal_ojs_url')
                                        ->label('OJS Base URL')
                                        ->default('https://ejournal.unida.gontor.ac.id')
                                        ->helperText('URL utama Open Journal Systems'),
                                    Forms\Components\Select::make('journal_sync_schedule')
                                        ->label('Jadwal Sync (Feed)')
                                        ->options([
                                            'daily' => 'Harian (setiap jam 03:00)',
                                            'weekly' => 'Mingguan (Minggu jam 03:00)',
                                            'disabled' => 'Nonaktif',
                                        ])
                                        ->default('daily')
                                        ->helperText('Sync cepat via RSS/Atom feed'),
                                    Forms\Components\Select::make('journal_scrape_schedule')
                                        ->label('Jadwal Scrape (Archive)')
                                        ->options([
                                            'weekly' => 'Mingguan (Minggu jam 02:00)',
                                            'monthly' => 'Bulanan (tanggal 1)',
                                            'disabled' => 'Nonaktif',
                                        ])
                                        ->default('weekly')
                                        ->helperText('Scrape lengkap dari halaman archive'),
                                ])->columns(2),
                            Forms\Components\Section::make('Aksi Manual Journal')
                                ->schema([
                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('sync_journal')
                                            ->label('Sync Feed')
                                            ->icon('heroicon-o-rss')
                                            ->color('info')
                                            ->action(function () {
                                                \Artisan::call('journals:sync');
                                                Notification::make()->title('Sync Journal (Feed) dimulai')->success()->send();
                                            }),
                                        Forms\Components\Actions\Action::make('scrape_journal')
                                            ->label('Scrape Archive')
                                            ->icon('heroicon-o-arrow-path')
                                            ->color('warning')
                                            ->requiresConfirmation()
                                            ->modalHeading('Scrape Journal Archive')
                                            ->modalDescription('Proses ini akan mengambil semua artikel dari archive OJS. Proses bisa memakan waktu lama. Lanjutkan?')
                                            ->action(function () {
                                                \Artisan::call('journals:scrape');
                                                Notification::make()->title('Scrape Journal (Archive) dimulai')->success()->send();
                                            }),
                                    ]),
                                ]),

                            Forms\Components\Section::make('Kubuku (E-Book)')
                                ->description('Integrasi dengan platform e-book Kubuku untuk koleksi digital. Sync dilakukan secara terjadwal di jam traffic rendah (dini hari).')
                                ->schema([
                                    Forms\Components\Toggle::make('kubuku_enabled')
                                        ->label('Aktifkan Integrasi')
                                        ->helperText('Tampilkan koleksi e-book dari Kubuku'),
                                    Forms\Components\Select::make('kubuku_sync_schedule')
                                        ->label('Jadwal Sync')
                                        ->options([
                                            'daily' => 'Harian (jam 02:00)',
                                            'weekly' => 'Mingguan (Sabtu jam 02:00)',
                                            'disabled' => 'Nonaktif',
                                        ])
                                        ->default('daily')
                                        ->helperText('Rekomendasi Kubuku: sync di jam traffic rendah'),
                                    Forms\Components\TextInput::make('kubuku_api_url')
                                        ->label('API URL')
                                        ->placeholder('https://api.kubuku.id/v1')
                                        ->helperText('Endpoint REST API Kubuku'),
                                    Forms\Components\TextInput::make('kubuku_api_key')
                                        ->label('API Key')
                                        ->password()
                                        ->revealable()
                                        ->helperText('API Key/Token dari Kubuku'),
                                    Forms\Components\TextInput::make('kubuku_library_id')
                                        ->label('Library ID')
                                        ->placeholder('unida-gontor')
                                        ->helperText('ID perpustakaan di sistem Kubuku'),
                                ])->columns(2),
                            Forms\Components\Section::make('Aksi Manual Kubuku')
                                ->schema([
                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('test_kubuku')
                                            ->label('Test Koneksi API')
                                            ->icon('heroicon-o-signal')
                                            ->color('info')
                                            ->action(function ($get) {
                                                $apiUrl = $get('kubuku_api_url');
                                                $apiKey = $get('kubuku_api_key');
                                                
                                                if (empty($apiUrl) || empty($apiKey)) {
                                                    Notification::make()
                                                        ->title('API URL dan API Key harus diisi')
                                                        ->danger()
                                                        ->send();
                                                    return;
                                                }
                                                
                                                try {
                                                    $response = \Http::timeout(15)->withHeaders([
                                                        'Authorization' => 'Bearer ' . $apiKey,
                                                        'Accept' => 'application/json',
                                                    ])->get(rtrim($apiUrl, '/'));
                                                    
                                                    if ($response->successful()) {
                                                        Notification::make()
                                                            ->title('Koneksi Berhasil!')
                                                            ->body('Kubuku API terhubung.')
                                                            ->success()
                                                            ->send();
                                                    } elseif ($response->status() === 401 || $response->status() === 403) {
                                                        Notification::make()
                                                            ->title('Autentikasi Gagal')
                                                            ->body('API Key tidak valid atau akses ditolak.')
                                                            ->warning()
                                                            ->send();
                                                    } else {
                                                        Notification::make()
                                                            ->title('Response: ' . $response->status())
                                                            ->body('Server merespons, periksa konfigurasi.')
                                                            ->warning()
                                                            ->send();
                                                    }
                                                } catch (\Exception $e) {
                                                    Notification::make()
                                                        ->title('Error Koneksi')
                                                        ->body('Tidak dapat terhubung: ' . $e->getMessage())
                                                        ->danger()
                                                        ->send();
                                                }
                                            }),
                                        Forms\Components\Actions\Action::make('sync_kubuku')
                                            ->label('Sync Sekarang')
                                            ->icon('heroicon-o-arrow-path')
                                            ->color('success')
                                            ->requiresConfirmation()
                                            ->modalHeading('Sync Kubuku E-Books')
                                            ->modalDescription('Proses ini akan mengambil data katalog e-book dari Kubuku. Lanjutkan?')
                                            ->action(function () {
                                                try {
                                                    \Artisan::call('kubuku:sync');
                                                    Notification::make()
                                                        ->title('Sync Kubuku dimulai')
                                                        ->body('Cek log untuk progress.')
                                                        ->success()
                                                        ->send();
                                                } catch (\Exception $e) {
                                                    Notification::make()
                                                        ->title('Gagal memulai sync')
                                                        ->body($e->getMessage())
                                                        ->danger()
                                                        ->send();
                                                }
                                            }),
                                    ]),
                                    Forms\Components\Placeholder::make('kubuku_info')
                                        ->label('Informasi')
                                        ->content('Dokumentasi API Kubuku akan tersedia dari pihak Kubuku. Sync otomatis berjalan sesuai jadwal yang dipilih di jam traffic rendah (rekomendasi: 02:00 dini hari).'),
                                ]),

                            Forms\Components\Section::make('Open Library (Internet Archive)')
                                ->description('Integrasi dengan Open Library untuk pencarian e-book global. API gratis dengan 4+ juta buku.')
                                ->schema([
                                    Forms\Components\Toggle::make('openlibrary_enabled')
                                        ->label('Aktifkan Integrasi')
                                        ->helperText('Tampilkan hasil dari Open Library di Global Search'),
                                    Forms\Components\TextInput::make('openlibrary_search_limit')
                                        ->label('Limit Hasil')
                                        ->numeric()
                                        ->default(10)
                                        ->minValue(5)
                                        ->maxValue(50)
                                        ->helperText('Jumlah maksimal hasil pencarian dari Open Library'),
                                ])->columns(2),
                            Forms\Components\Section::make('Test Open Library')
                                ->schema([
                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('test_openlibrary')
                                            ->label('Test Pencarian')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->color('info')
                                            ->form([
                                                Forms\Components\TextInput::make('test_query')
                                                    ->label('Kata Kunci')
                                                    ->placeholder('Contoh: Laravel')
                                                    ->required(),
                                            ])
                                            ->action(function (array $data) {
                                                try {
                                                    $response = \Http::timeout(10)->get('https://openlibrary.org/search.json', [
                                                        'q' => $data['test_query'],
                                                        'limit' => 5,
                                                    ]);
                                                    
                                                    if ($response->successful()) {
                                                        $result = $response->json();
                                                        $count = $result['numFound'] ?? 0;
                                                        $titles = collect($result['docs'] ?? [])->take(3)->pluck('title')->join(', ');
                                                        
                                                        Notification::make()
                                                            ->title('Berhasil! ' . number_format($count) . ' buku ditemukan')
                                                            ->body('Contoh: ' . \Str::limit($titles, 100))
                                                            ->success()
                                                            ->send();
                                                    } else {
                                                        Notification::make()
                                                            ->title('Gagal')
                                                            ->body('Status: ' . $response->status())
                                                            ->danger()
                                                            ->send();
                                                    }
                                                } catch (\Exception $e) {
                                                    Notification::make()
                                                        ->title('Error Koneksi')
                                                        ->body($e->getMessage())
                                                        ->danger()
                                                        ->send();
                                                }
                                            }),
                                    ]),
                                    Forms\Components\Placeholder::make('openlibrary_info')
                                        ->label('Informasi')
                                        ->content('Open Library menyediakan akses ke 4+ juta buku secara gratis. Hasil pencarian akan muncul di tab "External" pada Global Search.'),
                                ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Analytics')
                        ->icon('heroicon-o-chart-bar')
                        ->schema([
                            Forms\Components\Section::make('Google Analytics 4')
                                ->description('Integrasikan Google Analytics untuk melacak pengunjung website. Data analytics akan ditampilkan di Staff Portal.')
                                ->schema([
                                    Forms\Components\Toggle::make('ga_enabled')
                                        ->label('Aktifkan Google Analytics')
                                        ->helperText('Tampilkan tracking code GA4 di website publik'),
                                    Forms\Components\TextInput::make('ga_measurement_id')
                                        ->label('Measurement ID')
                                        ->placeholder('G-XXXXXXXXXX')
                                        ->helperText('Dapatkan dari Google Analytics > Admin > Data Streams'),
                                ])->columns(2),
                            Forms\Components\Section::make('Google Analytics Data API')
                                ->description('Untuk menampilkan statistik di Staff Portal, diperlukan akses ke Google Analytics Data API menggunakan Service Account.')
                                ->schema([
                                    Forms\Components\TextInput::make('ga_property_id')
                                        ->label('Property ID')
                                        ->placeholder('123456789')
                                        ->helperText('ID properti GA4 (bukan Measurement ID). Lihat di Admin > Property Settings'),
                                    Forms\Components\Textarea::make('ga_service_account_json')
                                        ->label('Service Account JSON')
                                        ->rows(6)
                                        ->placeholder('Paste isi file JSON service account di sini...')
                                        ->helperText('Buat Service Account di Google Cloud Console, lalu tambahkan ke GA4 sebagai Viewer'),
                                ]),
                            Forms\Components\Section::make('Panduan Setup')
                                ->collapsed()
                                ->schema([
                                    Forms\Components\Placeholder::make('ga_guide')
                                        ->label('')
                                        ->content(new \Illuminate\Support\HtmlString('
                                            <div class="text-sm space-y-3">
                                                <p class="font-semibold">Langkah Setup Google Analytics:</p>
                                                <ol class="list-decimal list-inside space-y-2 text-gray-600">
                                                    <li><strong>Buat Property GA4:</strong> Kunjungi <a href="https://analytics.google.com" target="_blank" class="text-blue-600 underline">analytics.google.com</a>, buat property baru</li>
                                                    <li><strong>Salin Measurement ID:</strong> Admin → Data Streams → Web → Measurement ID (G-XXXXXXXX)</li>
                                                    <li><strong>Buat Service Account:</strong> Kunjungi <a href="https://console.cloud.google.com/iam-admin/serviceaccounts" target="_blank" class="text-blue-600 underline">Google Cloud Console</a>, buat service account baru</li>
                                                    <li><strong>Enable Analytics Data API:</strong> Di Cloud Console, cari "Google Analytics Data API" dan enable</li>
                                                    <li><strong>Download JSON Key:</strong> Di Service Account, buat key baru (JSON format), download file-nya</li>
                                                    <li><strong>Tambah ke GA4:</strong> Di GA4 Admin → Property Access Management, tambahkan email service account sebagai Viewer</li>
                                                    <li><strong>Paste JSON:</strong> Buka file JSON yang didownload, salin isinya ke field di atas</li>
                                                </ol>
                                            </div>
                                        ')),
                                ]),
                        ]),
                ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setMany([
            'app_name' => $data['app_name'],
            'app_tagline' => $data['app_tagline'],
            'app_logo' => $data['app_logo'],
            'app_favicon' => $data['app_favicon'],
        ], 'general');

        Setting::setMany([
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'],
            'contact_whatsapp' => $data['contact_whatsapp'],
            'contact_address' => $data['contact_address'],
        ], 'contact');

        Setting::setMany([
            'social_instagram' => $data['social_instagram'],
            'social_facebook' => $data['social_facebook'],
            'social_twitter' => $data['social_twitter'],
            'social_youtube' => $data['social_youtube'],
        ], 'social');

        Setting::setMany([
            'google_enabled' => $data['google_enabled'],
            'google_client_id' => $data['google_client_id'],
            'google_client_secret' => $data['google_client_secret'],
            'google_allowed_domains' => $data['google_allowed_domains'],
        ], 'oauth');

        Setting::setMany([
            'plagiarism_enabled' => $data['plagiarism_enabled'],
            'plagiarism_provider' => $data['plagiarism_provider'],
            'plagiarism_pass_threshold' => $data['plagiarism_pass_threshold'],
            'plagiarism_warning_threshold' => $data['plagiarism_warning_threshold'],
            'plagiarism_min_words' => $data['plagiarism_min_words'],
            'plagiarism_max_file_size' => $data['plagiarism_max_file_size'],
            'plagiarism_head_librarian' => $data['plagiarism_head_librarian'],
            // iThenticate API
            'ithenticate_integration_name' => $data['ithenticate_integration_name'],
            'ithenticate_api_key' => $data['ithenticate_api_key'],
            'ithenticate_api_secret' => $data['ithenticate_api_secret'],
            'ithenticate_base_url' => $data['ithenticate_base_url'],
        ], 'plagiarism');

        Setting::setMany([
            'repo_enabled' => $data['repo_enabled'],
            'repo_oai_url' => $data['repo_oai_url'],
            'repo_sync_schedule' => $data['repo_sync_schedule'],
            'journal_enabled' => $data['journal_enabled'],
            'journal_ojs_url' => $data['journal_ojs_url'],
            'journal_sync_schedule' => $data['journal_sync_schedule'],
            'journal_scrape_schedule' => $data['journal_scrape_schedule'],
            'kubuku_enabled' => $data['kubuku_enabled'],
            'kubuku_sync_schedule' => $data['kubuku_sync_schedule'],
            'kubuku_api_url' => $data['kubuku_api_url'],
            'kubuku_api_key' => $data['kubuku_api_key'],
            'kubuku_library_id' => $data['kubuku_library_id'],
            // Open Library
            'openlibrary_enabled' => $data['openlibrary_enabled'],
            'openlibrary_search_limit' => $data['openlibrary_search_limit'],
        ], 'integration');

        Setting::setMany([
            'mail_mailer' => $data['mail_mailer'],
            'mail_host' => $data['mail_host'],
            'mail_port' => $data['mail_port'],
            'mail_username' => $data['mail_username'],
            'mail_password' => $data['mail_password'],
            'mail_encryption' => $data['mail_encryption'],
            'mail_from_address' => $data['mail_from_address'],
            'mail_from_name' => $data['mail_from_name'],
        ], 'mail');

        Setting::setMany([
            'ga_enabled' => $data['ga_enabled'] ?? false,
            'ga_measurement_id' => $data['ga_measurement_id'] ?? '',
            'ga_property_id' => $data['ga_property_id'] ?? '',
            'ga_service_account_json' => $data['ga_service_account_json'] ?? '',
        ], 'analytics');

        Notification::make()->title('Pengaturan berhasil disimpan')->success()->send();
    }

    protected function testEmail($get): void
    {
        try {
            $config = [
                'transport' => $get('mail_mailer') ?: 'smtp',
                'host' => $get('mail_host'),
                'port' => $get('mail_port'),
                'encryption' => $get('mail_encryption'),
                'username' => $get('mail_username'),
                'password' => $get('mail_password'),
            ];

            config(['mail.mailers.smtp' => $config]);
            config(['mail.from.address' => $get('mail_from_address')]);
            config(['mail.from.name' => $get('mail_from_name')]);

            \Mail::raw('Test email dari UNIDA Library. Jika Anda menerima email ini, konfigurasi SMTP sudah benar.', function ($message) use ($get) {
                $message->to($get('mail_from_address'))
                    ->subject('Test Email - UNIDA Library');
            });

            Notification::make()
                ->title('Email Terkirim!')
                ->body('Cek inbox ' . $get('mail_from_address'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Mengirim Email')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
