<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskTemplateResource\Pages;
use App\Models\TaskTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskTemplateResource extends Resource
{
    protected static ?string $model = TaskTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'Task Management';
    protected static ?string $navigationLabel = 'Template Task';
    protected static ?string $modelLabel = 'Template Task';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Template')
                ->icon('heroicon-o-document-duplicate')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Task')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Select::make('project_id')
                            ->label('Proyek')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('division_id')
                            ->label('Divisi')
                            ->relationship('division', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('priority')
                            ->label('Prioritas')
                            ->options([
                                'low' => '🟢 Low',
                                'medium' => '🟡 Medium',
                                'high' => '🟠 High',
                                'urgent' => '🔴 Urgent',
                            ])
                            ->default('medium')
                            ->native(false),
                    ]),
                    Forms\Components\RichEditor::make('description')
                        ->label('Deskripsi')
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Jadwal Otomatis')
                ->icon('heroicon-o-clock')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Select::make('frequency')
                            ->label('Frekuensi')
                            ->options([
                                'daily' => 'Harian',
                                'weekly' => 'Mingguan',
                                'monthly' => 'Bulanan',
                                'quarterly' => 'Per Kuartal',
                                'yearly' => 'Tahunan',
                            ])
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\TimePicker::make('schedule_time')
                            ->label('Waktu Generate')
                            ->default('08:00'),
                        Forms\Components\TextInput::make('due_days')
                            ->label('Deadline (hari)')
                            ->numeric()
                            ->default(1)
                            ->helperText('Jumlah hari dari tanggal generate'),
                    ]),
                    Forms\Components\CheckboxList::make('schedule_days')
                        ->label('Hari (untuk Mingguan)')
                        ->options([
                            0 => 'Minggu',
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                        ])
                        ->columns(7)
                        ->visible(fn (Forms\Get $get) => $get('frequency') === 'weekly'),
                    Forms\Components\TextInput::make('schedule_day')
                        ->label('Tanggal (untuk Bulanan)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(28)
                        ->visible(fn (Forms\Get $get) => in_array($get('frequency'), ['monthly', 'quarterly', 'yearly'])),
                ]),

            Forms\Components\Section::make('Penugasan')
                ->icon('heroicon-o-user')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('default_assignee')
                            ->label('Default Assignee')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Template aktif akan generate task otomatis'),
                    ]),
                ]),

            Forms\Components\Hidden::make('branch_id')->default(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->project?->name),
                Tables\Columns\TextColumn::make('frequency')
                    ->label('Frekuensi')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'daily' => 'Harian',
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                        'quarterly' => 'Per Kuartal',
                        'yearly' => 'Tahunan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assignee')
                    ->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('due_days')
                    ->label('Deadline')
                    ->suffix(' hari'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_generated_at')
                    ->label('Terakhir Generate')
                    ->since()
                    ->placeholder('Belum pernah'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('frequency')
                    ->options([
                        'daily' => 'Harian',
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\Action::make('generate')
                    ->label('Generate')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Task')
                    ->modalDescription('Task baru akan dibuat dari template ini.')
                    ->action(fn ($record) => $record->generateTask()),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaskTemplates::route('/'),
            'create' => Pages\CreateTaskTemplate::route('/create'),
            'edit' => Pages\EditTaskTemplate::route('/{record}/edit'),
        ];
    }
}
