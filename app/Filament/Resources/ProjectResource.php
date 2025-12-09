<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Task Management';
    protected static ?string $navigationLabel = 'Proyek';
    protected static ?string $modelLabel = 'Proyek';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Proyek')
                ->icon('heroicon-o-folder')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Proyek')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode')
                            ->maxLength(20)
                            ->placeholder('AUTO')
                            ->helperText('Kosongkan untuk auto-generate'),
                    ]),
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Select::make('division_id')
                            ->label('Divisi')
                            ->relationship('division', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('code')->maxLength(20),
                                Forms\Components\ColorPicker::make('color')->default('#6366f1'),
                            ]),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'on_hold' => 'Ditunda',
                                'completed' => 'Selesai',
                                'archived' => 'Arsip',
                            ])
                            ->default('active')
                            ->native(false),
                        Forms\Components\Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->default(1)
                            ->required(),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->displayFormat('d M Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->afterOrEqual('start_date'),
                    ]),
                    Forms\Components\RichEditor::make('description')
                        ->label('Deskripsi')
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Status Kustom')
                ->icon('heroicon-o-tag')
                ->description('Kelola status task untuk proyek ini')
                ->collapsed()
                ->schema([
                    Forms\Components\Repeater::make('statuses')
                        ->relationship()
                        ->label('')
                        ->schema([
                            Forms\Components\TextInput::make('name')->label('Nama')->required(),
                            Forms\Components\TextInput::make('slug')->label('Slug')->required(),
                            Forms\Components\ColorPicker::make('color')->label('Warna')->default('#6b7280'),
                            Forms\Components\TextInput::make('order')->label('Urutan')->numeric()->default(1),
                            Forms\Components\Toggle::make('is_default')->label('Default'),
                            Forms\Components\Toggle::make('is_done')->label('Selesai'),
                        ])
                        ->columns(6)
                        ->defaultItems(0)
                        ->addActionLabel('Tambah Status')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state) => $state['name'] ?? 'Status Baru'),
                ])
                ->visible(fn ($livewire) => $livewire instanceof Pages\EditProject),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->division?->name),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'on_hold' => 'warning',
                        'completed' => 'info',
                        'archived' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'Aktif',
                        'on_hold' => 'Ditunda',
                        'completed' => 'Selesai',
                        'archived' => 'Arsip',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->counts('tasks')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('completed_tasks')
                    ->label('Selesai')
                    ->getStateUsing(fn ($record) => $record->tasks()->whereHas('status', fn ($q) => $q->where('is_done', true))->count())
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->end_date?->isPast() && $record->status !== 'completed' ? 'danger' : null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'on_hold' => 'Ditunda',
                        'completed' => 'Selesai',
                        'archived' => 'Arsip',
                    ]),
                Tables\Filters\SelectFilter::make('division_id')
                    ->relationship('division', 'name')
                    ->label('Divisi')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_tasks')
                    ->label('Tasks')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('info')
                    ->url(fn ($record) => TaskResource::getUrl('index', ['tableFilters[project_id][value]' => $record->id])),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
