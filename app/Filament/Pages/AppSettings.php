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
                                            'ithenticate' => 'iThenticate',
                                            'turnitin' => 'Turnitin',
                                            'copyleaks' => 'Copyleaks',
                                        ])
                                        ->default('internal')
                                        ->helperText('Pilih layanan pengecekan plagiasi'),
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
                            Forms\Components\Section::make('iThenticate / Turnitin API')
                                ->description('Konfigurasi API untuk integrasi dengan iThenticate/Turnitin. Kosongkan jika menggunakan provider Internal.')
                                ->schema([
                                    Forms\Components\TextInput::make('ithenticate_integration_name')
                                        ->label('Integration/Scope Name')
                                        ->placeholder('Library-Portal-API')
                                        ->helperText('Nama scope yang dibuat di dashboard iThenticate'),
                                    Forms\Components\TextInput::make('ithenticate_api_key')
                                        ->label('Key Name')
                                        ->placeholder('SYSTEM-LIBRARY')
                                        ->helperText('Nama key dari TCA integration'),
                                    Forms\Components\TextInput::make('ithenticate_api_secret')
                                        ->label('Secret Key')
                                        ->password()
                                        ->revealable()
                                        ->helperText('Secret key dari TCA integration (disimpan terenkripsi)'),
                                    Forms\Components\TextInput::make('ithenticate_base_url')
                                        ->label('Base URL')
                                        ->default('https://unidagontor.turnitin.com')
                                        ->helperText('URL dashboard iThenticate Anda'),
                                ])->columns(2),
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

        Notification::make()->title('Pengaturan berhasil disimpan')->success()->send();
    }
}
