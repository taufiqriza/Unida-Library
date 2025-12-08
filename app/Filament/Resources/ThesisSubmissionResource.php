<?php

namespace App\Filament\Resources;

use App\Enums\ThesisType;
use App\Filament\Resources\ThesisSubmissionResource\Pages;
use App\Models\Department;
use App\Models\ThesisSubmission;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;

class ThesisSubmissionResource extends Resource
{
    protected static ?string $model = ThesisSubmission::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationGroup = 'E-Library';
    protected static ?string $navigationLabel = 'Unggah Mandiri';
    protected static ?string $modelLabel = 'Submission Tugas Akhir';
    protected static ?string $pluralModelLabel = 'Submission Tugas Akhir';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Tabs')->tabs([
                Tabs\Tab::make('Informasi Utama')->icon('heroicon-o-document-text')->schema([
                    Grid::make(3)->schema([
                        Placeholder::make('status_info')
                            ->label('Status')
                            ->content(fn(?ThesisSubmission $record) => $record?->status_label ?? '-')
                            ->visible(fn(?ThesisSubmission $record) => $record !== null),
                        Placeholder::make('member_info')
                            ->label('Diajukan oleh')
                            ->content(fn(?ThesisSubmission $record) => $record?->member?->name ?? '-')
                            ->visible(fn(?ThesisSubmission $record) => $record !== null),
                        Placeholder::make('submitted_at')
                            ->label('Tanggal Submit')
                            ->content(fn(?ThesisSubmission $record) => $record?->created_at?->format('d M Y H:i') ?? '-')
                            ->visible(fn(?ThesisSubmission $record) => $record !== null),
                    ]),
                    Select::make('type')
                        ->label('Jenis')
                        ->options(ThesisType::options())
                        ->required()
                        ->default('skripsi'),
                    TextInput::make('title')->label('Judul')->required()->maxLength(500)->columnSpanFull(),
                    TextInput::make('title_en')->label('Judul (English)')->maxLength(500)->columnSpanFull(),
                    Textarea::make('abstract')->label('Abstrak')->required()->rows(5)->columnSpanFull(),
                    Textarea::make('abstract_en')->label('Abstract (English)')->rows(5)->columnSpanFull(),
                    TextInput::make('keywords')->label('Kata Kunci')->placeholder('pisahkan dengan koma')->columnSpanFull(),
                ]),
                Tabs\Tab::make('Penulis & Pembimbing')->icon('heroicon-o-users')->schema([
                    Grid::make(2)->schema([
                        TextInput::make('author')->label('Nama Penulis')->required()->maxLength(255),
                        TextInput::make('nim')->label('NIM')->required()->maxLength(50),
                    ]),
                    Select::make('department_id')->label('Program Studi')
                        ->options(Department::pluck('name', 'id'))
                        ->required()->searchable(),
                    Grid::make(2)->schema([
                        TextInput::make('year')->label('Tahun')->numeric()->required()->default(date('Y')),
                        DatePicker::make('defense_date')->label('Tanggal Sidang'),
                    ]),
                    Section::make('Dosen Pembimbing')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('advisor1')->label('Pembimbing 1')->required()->maxLength(255),
                            TextInput::make('advisor2')->label('Pembimbing 2')->maxLength(255),
                        ]),
                    ]),
                    Section::make('Dosen Penguji')->schema([
                        Grid::make(3)->schema([
                            TextInput::make('examiner1')->label('Penguji 1')->maxLength(255),
                            TextInput::make('examiner2')->label('Penguji 2')->maxLength(255),
                            TextInput::make('examiner3')->label('Penguji 3')->maxLength(255),
                        ]),
                    ]),
                ]),
                Tabs\Tab::make('File Dokumen')->icon('heroicon-o-document-arrow-up')->schema([
                    Grid::make(2)->schema([
                        FileUpload::make('cover_file')
                            ->label('Cover')
                            ->directory('thesis-submissions/covers')
                            ->image()
                            ->maxSize(2048)
                            ->helperText('Format: JPG/PNG, Max: 2MB'),
                        FileUpload::make('approval_file')
                            ->label('Lembar Pengesahan')
                            ->directory('thesis-submissions/approvals')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->helperText('Format: PDF, Max: 5MB'),
                    ]),
                    Grid::make(2)->schema([
                        FileUpload::make('preview_file')
                            ->label('BAB 1-3 (Preview)')
                            ->directory('thesis-submissions/previews')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(20480)
                            ->helperText('Format: PDF, Max: 20MB - Ditampilkan publik'),
                        FileUpload::make('fulltext_file')
                            ->label('Full Text')
                            ->directory('thesis-submissions/fulltext')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(51200)
                            ->helperText('Format: PDF, Max: 50MB'),
                    ]),
                ]),
                Tabs\Tab::make('Pengaturan Akses')->icon('heroicon-o-eye')->schema([
                    Section::make('Visibilitas File')
                        ->description('Atur file mana yang dapat diakses publik setelah dipublikasikan')
                        ->schema([
                            Grid::make(2)->schema([
                                Toggle::make('cover_visible')
                                    ->label('Cover dapat diakses publik')
                                    ->default(true)
                                    ->helperText('Cover akan ditampilkan di halaman detail'),
                                Toggle::make('approval_visible')
                                    ->label('Lembar Pengesahan dapat diakses publik')
                                    ->default(false)
                                    ->helperText('Biasanya hanya untuk internal'),
                            ]),
                            Grid::make(2)->schema([
                                Toggle::make('preview_visible')
                                    ->label('BAB 1-3 dapat diakses publik')
                                    ->default(true)
                                    ->helperText('Preview untuk pembaca'),
                                Toggle::make('fulltext_visible')
                                    ->label('Full Text dapat diakses publik')
                                    ->default(false)
                                    ->helperText('Override permintaan user'),
                            ]),
                        ]),
                    Section::make('Permintaan User')
                        ->schema([
                            Toggle::make('allow_fulltext_public')
                                ->label('User meminta akses publik Full Text')
                                ->disabled()
                                ->helperText('Diisi oleh user saat submit'),
                        ]),
                ])->visible(fn(?ThesisSubmission $record) => $record !== null),
                Tabs\Tab::make('Review')->icon('heroicon-o-clipboard-document-check')->schema([
                    Placeholder::make('reviewer_info')
                        ->label('Direview oleh')
                        ->content(fn(?ThesisSubmission $record) => $record?->reviewer?->name ?? '-'),
                    Placeholder::make('reviewed_at_info')
                        ->label('Tanggal Review')
                        ->content(fn(?ThesisSubmission $record) => $record?->reviewed_at?->format('d M Y H:i') ?? '-'),
                    Textarea::make('review_notes')->label('Catatan Review')->rows(3)->columnSpanFull(),
                    Textarea::make('rejection_reason')->label('Alasan Penolakan')->rows(3)->columnSpanFull(),
                ])->visible(fn(?ThesisSubmission $record) => $record !== null),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_file')
                    ->label('')
                    ->disk('public')
                    ->width(40)
                    ->height(50)
                    ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=TA&background=6366f1&color=fff'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->limit(50)
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn(ThesisSubmission $record) => $record->title),
                Tables\Columns\TextColumn::make('author')
                    ->label('Penulis')
                    ->searchable()
                    ->description(fn(ThesisSubmission $record) => $record->nim),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Prodi')
                    ->sortable()
                    ->toggleable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => ThesisType::tryFrom($state)?->degree() ?? $state)
                    ->color(fn(string $state) => match($state) {
                        'skripsi' => 'info',
                        'tesis' => 'warning',
                        'disertasi' => 'success',
                        default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(ThesisSubmission $record) => $record->status_label)
                    ->color(fn(ThesisSubmission $record) => $record->status_color),
                Tables\Columns\IconColumn::make('files_complete')
                    ->label('File')
                    ->boolean()
                    ->getStateUsing(fn(ThesisSubmission $record) => 
                        $record->cover_file && $record->approval_file && $record->preview_file
                    )
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ThesisSubmission::getStatuses())
                    ->multiple(),
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(ThesisType::options()),
                SelectFilter::make('department_id')
                    ->label('Prodi')
                    ->options(Department::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->url(fn(ThesisSubmission $record) => static::getUrl('review', ['record' => $record]))
                    ->visible(fn(ThesisSubmission $record) => $record->canReview()),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (ThesisSubmission $record) {
                        $record->approve(auth()->id());
                        Notification::make()->title('Submission disetujui')->success()->send();
                    })
                    ->visible(fn(ThesisSubmission $record) => $record->canReview()),
                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-globe-alt')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Publish ke E-Thesis')
                    ->modalDescription('Submission akan dipublikasikan ke koleksi E-Thesis.')
                    ->action(function (ThesisSubmission $record) {
                        $ethesis = $record->publish(auth()->id());
                        if ($ethesis) {
                            Notification::make()->title('Berhasil dipublikasikan')->success()->send();
                        }
                    })
                    ->visible(fn(ThesisSubmission $record) => $record->isApproved()),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThesisSubmissions::route('/'),
            'create' => Pages\CreateThesisSubmission::route('/create'),
            'edit' => Pages\EditThesisSubmission::route('/{record}/edit'),
            'review' => Pages\ReviewThesisSubmission::route('/{record}/review'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['submitted', 'under_review'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
