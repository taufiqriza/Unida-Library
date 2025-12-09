<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use App\Models\TaskStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Task Management';
    protected static ?string $navigationLabel = 'Tasks';
    protected static ?string $modelLabel = 'Task';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Task')
                ->icon('heroicon-o-clipboard-document-list')
                ->schema([
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\Select::make('project_id')
                            ->label('Proyek')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('status_id', null)),
                        Forms\Components\Select::make('division_id')
                            ->label('Divisi')
                            ->relationship('division', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'task' => '📋 Task',
                                'bug' => '🐛 Bug',
                                'feature' => '✨ Feature',
                                'improvement' => '📈 Improvement',
                            ])
                            ->default('task')
                            ->native(false),
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
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Task')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->placeholder('Masukkan judul task...'),
                    Forms\Components\RichEditor::make('description')
                        ->label('Deskripsi')
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'codeBlock'])
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Penugasan & Jadwal')
                ->icon('heroicon-o-user-group')
                ->schema([
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\Select::make('status_id')
                            ->label('Status')
                            ->options(function (Forms\Get $get) {
                                $projectId = $get('project_id');
                                return TaskStatus::query()
                                    ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
                                    ->when(!$projectId, fn ($q) => $q->whereNull('project_id'))
                                    ->orderBy('order')
                                    ->pluck('name', 'id');
                            })
                            ->default(fn () => TaskStatus::whereNull('project_id')->where('is_default', true)->first()?->id)
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('assigned_to')
                            ->label('Ditugaskan ke')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Mulai')
                            ->native(false)
                            ->displayFormat('d M Y'),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Deadline')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->afterOrEqual('start_date'),
                    ]),
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\TextInput::make('estimated_hours')
                            ->label('Estimasi (jam)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.5),
                        Forms\Components\TextInput::make('actual_hours')
                            ->label('Aktual (jam)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.5),
                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Task')
                            ->relationship('parent', 'title', fn (Builder $query, $record) => 
                                $query->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                            )
                            ->searchable()
                            ->preload(),
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags')
                            ->separator(','),
                    ]),
                ]),

            Forms\Components\Section::make('Lampiran')
                ->icon('heroicon-o-paper-clip')
                ->collapsed()
                ->schema([
                    Forms\Components\Repeater::make('attachments')
                        ->relationship()
                        ->label('')
                        ->schema([
                            Forms\Components\FileUpload::make('file_path')
                                ->label('File')
                                ->directory('task-attachments')
                                ->required(),
                            Forms\Components\TextInput::make('name')
                                ->label('Nama File')
                                ->maxLength(255),
                            Forms\Components\Hidden::make('user_id')->default(auth()->id()),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Tambah Lampiran'),
                ]),

            Forms\Components\Hidden::make('branch_id')->default(1),
            Forms\Components\Hidden::make('reported_by')->default(auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->color('gray'),
                Tables\Columns\TextColumn::make('type')
                    ->label('')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'bug' => '🐛',
                        'feature' => '✨',
                        'improvement' => '📈',
                        default => '📋',
                    })
                    ->tooltip(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('title')
                    ->label('Task')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) => $record->project?->name),
                Tables\Columns\TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => $record->status?->color ?? 'gray'),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assignee')
                    ->placeholder('Unassigned')
                    ->color(fn ($state) => $state ? null : 'gray'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null)
                    ->icon(fn ($record) => $record->isOverdue() ? 'heroicon-o-exclamation-triangle' : null),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('💬')
                    ->counts('comments')
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->relationship('project', 'name')
                    ->label('Proyek')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status_id')
                    ->relationship('status', 'name')
                    ->label('Status')
                    ->preload(),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'urgent' => 'Urgent',
                        'high' => 'High',
                        'medium' => 'Medium',
                        'low' => 'Low',
                    ]),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignee', 'name')
                    ->label('Assignee')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'task' => 'Task',
                        'bug' => 'Bug',
                        'feature' => 'Feature',
                        'improvement' => 'Improvement',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(fn (Builder $query) => $query->overdue())
                    ->toggle(),
                Tables\Filters\Filter::make('due_soon')
                    ->label('Due Soon (3 days)')
                    ->query(fn (Builder $query) => $query->dueSoon(3))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('change_status')
                    ->label('Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->form([
                        Forms\Components\Select::make('status_id')
                            ->label('Status Baru')
                            ->options(fn ($record) => TaskStatus::query()
                                ->when($record->project_id, fn ($q) => $q->where('project_id', $record->project_id))
                                ->when(!$record->project_id, fn ($q) => $q->whereNull('project_id'))
                                ->orderBy('order')
                                ->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(fn ($record, array $data) => $record->update(['status_id' => $data['status_id']])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_status')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status_id')
                                ->label('Status')
                                ->options(TaskStatus::whereNull('project_id')->orderBy('order')->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(fn ($records, array $data) => $records->each->update(['status_id' => $data['status_id']])),
                    Tables\Actions\BulkAction::make('bulk_assign')
                        ->label('Assign ke')
                        ->icon('heroicon-o-user')
                        ->form([
                            Forms\Components\Select::make('assigned_to')
                                ->label('User')
                                ->relationship('assignee', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(fn ($records, array $data) => $records->each->update(['assigned_to' => $data['assigned_to']])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TaskResource\RelationManagers\CommentsRelationManager::class,
            TaskResource\RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'view' => Pages\ViewTask::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('status', fn ($q) => $q->where('is_done', false))->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $overdue = static::getModel()::overdue()->count();
        return $overdue > 0 ? 'danger' : 'primary';
    }
}
