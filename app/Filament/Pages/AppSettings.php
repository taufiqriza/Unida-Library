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

        Notification::make()->title('Pengaturan berhasil disimpan')->success()->send();
    }
}
