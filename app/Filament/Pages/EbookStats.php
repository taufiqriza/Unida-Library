<?php

namespace App\Filament\Pages;

use App\Models\Ebook;
use App\Models\EbookDownload;
use App\Models\Ethesis;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class EbookStats extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'E-Library';
    protected static ?string $navigationLabel = 'Statistik';
    protected static ?int $navigationSort = 12;
    protected static string $view = 'filament.pages.ebook-stats';

    public function table(Table $table): Table
    {
        return $table
            ->query(Ebook::query()->withCount('downloads'))
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('file_format')->label('Format')->badge(),
                Tables\Columns\TextColumn::make('downloads_count')->label('Download')->sortable()->badge()->color('success'),
                Tables\Columns\TextColumn::make('view_count')->label('View')->sortable(),
                Tables\Columns\TextColumn::make('access_type')->label('Akses')->badge(),
            ])
            ->defaultSort('downloads_count', 'desc');
    }

    public function getTotalEbooks(): int { return Ebook::count(); }
    public function getTotalDownloads(): int { return EbookDownload::count(); }
    public function getThisMonthDownloads(): int { return EbookDownload::whereMonth('created_at', now()->month)->count(); }
    public function getPopularFormat(): string { return Ebook::selectRaw('file_format, count(*) as total')->groupBy('file_format')->orderByDesc('total')->first()?->file_format ?? '-'; }

    // E-Thesis Stats
    public function getTotalEthesis(): int { return Ethesis::count(); }
    public function getEthesisByType(): array
    {
        return [
            'skripsi' => Ethesis::where('type', 'skripsi')->count(),
            'tesis' => Ethesis::where('type', 'tesis')->count(),
            'disertasi' => Ethesis::where('type', 'disertasi')->count(),
        ];
    }
    public function getTotalEthesisViews(): int { return Ethesis::sum('views'); }
    public function getTotalEthesisDownloads(): int { return Ethesis::sum('downloads'); }
}
