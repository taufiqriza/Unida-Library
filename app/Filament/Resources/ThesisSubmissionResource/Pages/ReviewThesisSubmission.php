<?php

namespace App\Filament\Resources\ThesisSubmissionResource\Pages;

use App\Enums\ThesisType;
use App\Filament\Resources\ThesisSubmissionResource;
use App\Models\ThesisSubmission;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Storage;

class ReviewThesisSubmission extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static string $resource = ThesisSubmissionResource::class;
    protected static string $view = 'filament.resources.thesis-submission-resource.pages.review-thesis-submission';

    public ThesisSubmission $record;

    public function mount(ThesisSubmission $record): void
    {
        $this->record = $record;
        
        if ($record->isSubmitted()) {
            $record->startReview(auth()->id());
            $this->record->refresh();
        }
    }

    public function getTitle(): string
    {
        return 'Review Submission';
    }

    public function getSubheading(): ?string
    {
        return $this->record->title;
    }

    public function submissionInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                InfoSection::make('Status & Info')
                    ->schema([
                        InfoGrid::make(4)->schema([
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn(ThesisSubmission $record) => $record->status_color),
                            TextEntry::make('type')
                                ->label('Jenis')
                                ->formatStateUsing(fn(ThesisSubmission $record) => $record->getTypeFullLabel())
                                ->badge()
                                ->color(fn(ThesisSubmission $record) => $record->getThesisTypeEnum()?->color() ?? 'gray'),
                            TextEntry::make('member.name')
                                ->label('Diajukan oleh'),
                            TextEntry::make('created_at')
                                ->label('Tanggal Submit')
                                ->dateTime('d M Y H:i'),
                        ]),
                    ])->collapsible(),

                InfoSection::make('Informasi Tugas Akhir')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Judul')
                            ->columnSpanFull()
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large),
                        TextEntry::make('title_en')
                            ->label('Judul (English)')
                            ->columnSpanFull()
                            ->placeholder('-'),
                        InfoGrid::make(3)->schema([
                            TextEntry::make('year')->label('Tahun'),
                            TextEntry::make('defense_date')->label('Tanggal Sidang')->date('d M Y')->placeholder('-'),
                            TextEntry::make('keywords')->label('Kata Kunci')->placeholder('-'),
                        ]),
                        TextEntry::make('abstract')
                            ->label('Abstrak')
                            ->columnSpanFull()
                            ->markdown()
                            ->prose(),
                        TextEntry::make('abstract_en')
                            ->label('Abstract (English)')
                            ->columnSpanFull()
                            ->markdown()
                            ->prose()
                            ->placeholder('-'),
                    ])->collapsible(),

                InfoSection::make('Penulis & Pembimbing')
                    ->schema([
                        InfoGrid::make(3)->schema([
                            TextEntry::make('author')->label('Nama Penulis'),
                            TextEntry::make('nim')->label('NIM'),
                            TextEntry::make('department.name')->label('Program Studi'),
                        ]),
                        InfoGrid::make(2)->schema([
                            TextEntry::make('advisor1')->label('Pembimbing 1'),
                            TextEntry::make('advisor2')->label('Pembimbing 2')->placeholder('-'),
                        ]),
                        InfoGrid::make(3)->schema([
                            TextEntry::make('examiner1')->label('Penguji 1')->placeholder('-'),
                            TextEntry::make('examiner2')->label('Penguji 2')->placeholder('-'),
                            TextEntry::make('examiner3')->label('Penguji 3')->placeholder('-'),
                        ]),
                    ])->collapsible(),

                InfoSection::make('File Dokumen')
                    ->schema([
                        InfoGrid::make(4)->schema([
                            ImageEntry::make('cover_file')
                                ->label('Cover')
                                ->disk('thesis')
                                ->height(150)
                                ->defaultImageUrl('https://ui-avatars.com/api/?name=No+Cover&background=e5e7eb&color=9ca3af'),
                            TextEntry::make('approval_file')
                                ->label('Lembar Pengesahan')
                                ->formatStateUsing(fn($state) => $state ? '📄 Ada' : '-')
                                ->color('primary'),
                            TextEntry::make('preview_file')
                                ->label('BAB 1-3')
                                ->formatStateUsing(fn($state) => $state ? '📄 Ada' : '-')
                                ->color('primary'),
                            TextEntry::make('fulltext_file')
                                ->label('Full Text')
                                ->formatStateUsing(fn($state) => $state ? '📄 Ada' : '-')
                                ->color('primary'),
                        ]),
                    ])->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->form([
                    Section::make('Pengaturan Visibilitas')
                        ->description('Atur file mana yang dapat diakses publik')
                        ->schema([
                            Grid::make(2)->schema([
                                Toggle::make('cover_visible')
                                    ->label('Cover publik')
                                    ->default($this->record->cover_visible ?? true),
                                Toggle::make('preview_visible')
                                    ->label('BAB 1-3 publik')
                                    ->default($this->record->preview_visible ?? true),
                                Toggle::make('approval_visible')
                                    ->label('Pengesahan publik')
                                    ->default($this->record->approval_visible ?? false),
                                Toggle::make('fulltext_visible')
                                    ->label('Full Text publik')
                                    ->default($this->record->fulltext_visible ?? false)
                                    ->helperText($this->record->allow_fulltext_public ? 'User meminta akses publik' : ''),
                            ]),
                        ]),
                    Textarea::make('notes')
                        ->label('Catatan (opsional)')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'cover_visible' => $data['cover_visible'] ?? true,
                        'approval_visible' => $data['approval_visible'] ?? false,
                        'preview_visible' => $data['preview_visible'] ?? true,
                        'fulltext_visible' => $data['fulltext_visible'] ?? false,
                    ]);
                    $this->record->approve(auth()->id(), $data['notes'] ?? null);
                    Notification::make()->title('Submission disetujui')->success()->send();
                    $this->redirect(ThesisSubmissionResource::getUrl('index'));
                })
                ->visible(fn() => $this->record->canReview()),

            Actions\Action::make('revision')
                ->label('Minta Revisi')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->form([
                    Textarea::make('notes')
                        ->label('Catatan Revisi')
                        ->required()
                        ->rows(4)
                        ->placeholder('Jelaskan apa yang perlu direvisi oleh mahasiswa...'),
                ])
                ->action(function (array $data) {
                    $this->record->requestRevision(auth()->id(), $data['notes']);
                    Notification::make()->title('Permintaan revisi dikirim')->warning()->send();
                    $this->redirect(ThesisSubmissionResource::getUrl('index'));
                })
                ->visible(fn() => $this->record->canReview()),

            Actions\Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Textarea::make('reason')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->rows(4)
                        ->placeholder('Jelaskan alasan penolakan submission ini...'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Tolak Submission')
                ->modalDescription('Submission yang ditolak tidak dapat direvisi lagi.')
                ->action(function (array $data) {
                    $this->record->reject(auth()->id(), $data['reason']);
                    Notification::make()->title('Submission ditolak')->danger()->send();
                    $this->redirect(ThesisSubmissionResource::getUrl('index'));
                })
                ->visible(fn() => $this->record->canReview()),

            Actions\Action::make('publish')
                ->label('Publish ke E-Thesis')
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Publish ke E-Thesis')
                ->modalDescription('Submission akan dipublikasikan ke koleksi E-Thesis dan dapat diakses sesuai pengaturan visibilitas.')
                ->action(function () {
                    $ethesis = $this->record->publish(auth()->id());
                    if ($ethesis) {
                        Notification::make()->title('Berhasil dipublikasikan ke E-Thesis')->success()->send();
                        $this->redirect(ThesisSubmissionResource::getUrl('index'));
                    }
                })
                ->visible(fn() => $this->record->isApproved()),

            Actions\Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(ThesisSubmissionResource::getUrl('index')),
        ];
    }
}
