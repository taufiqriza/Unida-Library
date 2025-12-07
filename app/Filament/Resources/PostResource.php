<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?string $modelLabel = 'Berita';
    protected static ?string $pluralModelLabel = 'Berita';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('title')->label('Judul')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('excerpt')->label('Ringkasan')->rows(2),
                Forms\Components\RichEditor::make('content')->label('Konten')->required()->columnSpanFull(),
                Forms\Components\FileUpload::make('image')->label('Gambar')->image()->directory('posts'),
                Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft'),
                Forms\Components\DateTimePicker::make('published_at')->label('Tanggal Publish'),
                Forms\Components\Hidden::make('user_id')->default(auth()->id()),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('')->circular(),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('user.name')->label('Penulis'),
                Tables\Columns\BadgeColumn::make('status')->colors(['warning' => 'draft', 'success' => 'published']),
                Tables\Columns\TextColumn::make('published_at')->label('Publish')->dateTime('d M Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published']),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
