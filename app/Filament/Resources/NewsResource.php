<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use App\Models\NewsCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?string $navigationLabel = 'Berita';
    protected static ?string $modelLabel = 'Berita';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Konten')->schema([
                TextInput::make('title')->label('Judul')
                    ->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->label('Slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                Select::make('news_category_id')->label('Kategori')
                    ->options(NewsCategory::where('is_active', true)->pluck('name', 'id'))
                    ->searchable(),
                Textarea::make('excerpt')->label('Ringkasan')->rows(2)->maxLength(500),
                RichEditor::make('content')->label('Konten')
                    ->required()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'link', 'orderedList', 'bulletList', 'h2', 'h3', 'blockquote', 'codeBlock', 'attachFiles'])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('news-attachments')
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Media & Publikasi')->schema([
                FileUpload::make('featured_image')->label('Gambar Utama')
                    ->image()->directory('news')->maxSize(2048),
                Select::make('status')->label('Status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])
                    ->default('draft')->required(),
                DateTimePicker::make('published_at')->label('Tanggal Publikasi'),
                Toggle::make('is_featured')->label('Featured')->helperText('Tampilkan di halaman utama'),
                Toggle::make('is_pinned')->label('Pinned')->helperText('Sematkan di atas'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')->label('Gambar')->circular(),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable()->limit(40),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge(),
                Tables\Columns\TextColumn::make('author.name')->label('Penulis'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn(string $state) => match($state) { 'published' => 'success', 'draft' => 'warning', 'archived' => 'gray', default => 'gray' }),
                Tables\Columns\IconColumn::make('is_featured')->label('Featured')->boolean(),
                Tables\Columns\IconColumn::make('is_pinned')->label('Pinned')->boolean(),
                Tables\Columns\TextColumn::make('views')->label('Views')->sortable(),
                Tables\Columns\TextColumn::make('published_at')->label('Publikasi')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']),
                Tables\Filters\SelectFilter::make('news_category_id')->label('Kategori')->options(NewsCategory::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(News $record) => $record->status !== 'published')
                    ->action(fn(News $record) => $record->update(['status' => 'published', 'published_at' => now()])),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'draft')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
