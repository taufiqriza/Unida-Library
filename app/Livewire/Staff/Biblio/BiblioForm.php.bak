<?php

namespace App\Livewire\Staff\Biblio;

use App\Models\Book;
use App\Models\Author;
use App\Models\Subject;
use App\Models\Publisher;
use App\Models\Place;
use App\Models\Branch;
use App\Models\MediaType;
use App\Models\ContentType;
use App\Models\Frequency;
use App\Services\CallNumberService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

class BiblioForm extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public ?Book $book = null;
    public ?array $data = [];

    public function mount($id = null): void
    {
        if ($id) {
            $this->book = Book::withoutGlobalScopes()->with(['authors', 'subjects'])->findOrFail($id);
            $this->form->fill([
                ...$this->book->toArray(),
                'authors' => $this->book->authors->pluck('id')->toArray(),
                'subjects' => $this->book->subjects->pluck('id')->toArray(),
            ]);
        } else {
            $this->form->fill([
                'branch_id' => auth()->user()->branch_id ?? 1,
                'is_opac_visible' => true,
                'language' => 'id',
                'item_qty' => 1,
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Bibliography')
                    ->tabs([
                        // Tab 1: Informasi Utama
                        Forms\Components\Tabs\Tab::make('Informasi Utama')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Select::make('branch_id')
                                    ->label('Lokasi')
                                    ->options(function () {
                                        $user = auth()->user();
                                        // Only super_admin can see all branches
                                        if ($user->role === 'super_admin') {
                                            return Branch::orderBy('name')->pluck('name', 'id');
                                        }
                                        // Others only see their own branch
                                        return Branch::where('id', $user->branch_id)->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->disabled(fn () => auth()->user()->role !== 'super_admin')
                                    ->dehydrated()
                                    ->default(fn () => auth()->user()->branch_id ?? 1),
                                Forms\Components\Select::make('media_type_id')
                                    ->label('GMD')
                                    ->options(MediaType::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->helperText('General Material Designation')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->required(),
                                        Forms\Components\TextInput::make('code')->maxLength(10),
                                    ])
                                    ->createOptionUsing(fn (array $data) => MediaType::create($data)->id),
                                Forms\Components\Select::make('content_type_id')
                                    ->label('Content Type')
                                    ->options(ContentType::pluck('name', 'id'))
                                    ->searchable()
                                    ->native(false)
                                    ->helperText('RDA Content Type'),
                                Forms\Components\TextInput::make('item_qty')
                                    ->label('Jml Eksemplar')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->visible(fn () => !$this->book),
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(500)
                                    ->live(onBlur: true)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('edition')
                                    ->label('Edisi'),
                                Forms\Components\TextInput::make('spec_detail_info')
                                    ->label('Info Detail Khusus')
                                    ->helperText('Untuk terbitan berseri: Vol, No'),
                            ])->columns(4),

                        // Tab 2: Penulis & Subjek
                        Forms\Components\Tabs\Tab::make('Penulis & Subjek')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Forms\Components\Select::make('authors')
                                    ->label('Penulis')
                                    ->multiple()
                                    ->options(Author::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->live()
                                    ->helperText('Tambahkan penulis utama dan tambahan')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->label('Nama Penulis')->required(),
                                        Forms\Components\Radio::make('type')
                                            ->label('Tipe Kepengarangan')
                                            ->options([
                                                'personal' => 'Personal Name (p)',
                                                'organizational' => 'Organizational Body (o)',
                                                'conference' => 'Conference (c)',
                                            ])
                                            ->default('personal')
                                            ->inline(),
                                    ])
                                    ->createOptionUsing(fn (array $data) => Author::create($data)->id),
                                Forms\Components\Select::make('subjects')
                                    ->label('Subjek/Topik')
                                    ->multiple()
                                    ->options(Subject::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->helperText('Subjek atau topik buku')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->label('Subjek')->required(),
                                        Forms\Components\Radio::make('type')
                                            ->label('Tipe Subjek')
                                            ->options([
                                                'topic' => 'Topik (t)',
                                                'geographic' => 'Geografis (g)',
                                                'name' => 'Nama (n)',
                                                'temporal' => 'Temporal (tm)',
                                                'genre' => 'Genre (gr)',
                                            ])
                                            ->default('topic')
                                            ->columns(2),
                                    ])
                                    ->createOptionUsing(fn (array $data) => Subject::create($data)->id),
                            ])->columns(2),

                        // Tab 3: Penerbitan
                        Forms\Components\Tabs\Tab::make('Penerbitan')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Select::make('publisher_id')
                                    ->label('Penerbit')
                                    ->options(Publisher::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->label('Nama Penerbit')->required(),
                                    ])
                                    ->createOptionUsing(fn (array $data) => Publisher::create($data)->id),
                                Forms\Components\Select::make('place_id')
                                    ->label('Tempat Terbit')
                                    ->options(Place::pluck('name', 'id'))
                                    ->searchable()
                                    ->native(false)
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->label('Nama Kota')->required(),
                                    ])
                                    ->createOptionUsing(fn (array $data) => Place::create($data)->id),
                                Forms\Components\TextInput::make('publish_year')
                                    ->label('Tahun Terbit')
                                    ->maxLength(4)
                                    ->numeric(),
                                Forms\Components\TextInput::make('collation')
                                    ->label('Kolasi')
                                    ->helperText('Contoh: xii, 350 hlm. : ilus. ; 21 cm')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('isbn')
                                    ->label('ISBN/ISSN')
                                    ->maxLength(20),
                                Forms\Components\Select::make('language')
                                    ->label('Bahasa')
                                    ->options([
                                        'id' => 'Indonesia',
                                        'en' => 'English',
                                        'ar' => 'Arabic',
                                        'zh' => 'Chinese',
                                        'ja' => 'Japanese',
                                    ])
                                    ->default('id')
                                    ->native(false)
                                    ->searchable(),
                            ])->columns(3),

                        // Tab 4: Klasifikasi
                        Forms\Components\Tabs\Tab::make('Klasifikasi')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('classification')
                                            ->label('No. Klasifikasi')
                                            ->maxLength(40)
                                            ->hint('Nomor DDC/UDC')
                                            ->hintIcon('heroicon-o-information-circle')
                                            ->live(onBlur: true)
                                            ->suffixAction(
                                                Forms\Components\Actions\Action::make('searchDdc')
                                                    ->label('Cari DDC')
                                                    ->icon('heroicon-o-book-open')
                                                    ->color('primary')
                                                    ->modalHeading('DDC Lookup - Dewey Decimal Classification')
                                                    ->modalDescription('Cari dan pilih nomor klasifikasi yang sesuai')
                                                    ->modalWidth('3xl')
                                                    ->modalSubmitAction(false)
                                                    ->modalCancelActionLabel('Tutup')
                                                    ->modalContent(fn () => view('filament.components.ddc-lookup-modal'))
                                            ),
                                        Forms\Components\TextInput::make('call_number')
                                            ->label('No. Panggil')
                                            ->hint('Format: S Klasifikasi Penulis Judul')
                                            ->hintIcon('heroicon-o-information-circle')
                                            ->maxLength(50)
                                            ->placeholder('Klik Generate →')
                                            ->suffixAction(
                                                Forms\Components\Actions\Action::make('generateCallNumber')
                                                    ->label('Generate')
                                                    ->icon('heroicon-o-sparkles')
                                                    ->color('success')
                                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                                        $classification = $get('classification');
                                                        $title = $get('title');
                                                        $authors = $get('authors');
                                                        
                                                        $sor = '';
                                                        if (!empty($authors) && is_array($authors)) {
                                                            $author = Author::find($authors[0]);
                                                            $sor = $author?->name ?? '';
                                                        }
                                                        
                                                        $authorCode = CallNumberService::getAuthorCode($sor);
                                                        $titleCode = CallNumberService::getTitleCode($title);
                                                        
                                                        $parts = array_filter(['S', $classification, $authorCode, $titleCode]);
                                                        $callNumber = implode(' ', $parts);
                                                        $set('call_number', $callNumber);
                                                        
                                                        Notification::make()
                                                            ->title('Nomor panggil berhasil di-generate')
                                                            ->success()
                                                            ->send();
                                                    })
                                            ),
                                    ]),
                                Forms\Components\TextInput::make('series_title')
                                    ->label('Judul Seri')
                                    ->maxLength(200),
                                Forms\Components\Select::make('frequency_id')
                                    ->label('Frekuensi')
                                    ->options(Frequency::pluck('name', 'id'))
                                    ->native(false)
                                    ->hint('Untuk terbitan berseri'),
                            ])->columns(2),

                        // Tab 5: Abstrak & Catatan
                        Forms\Components\Tabs\Tab::make('Abstrak & Catatan')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Textarea::make('abstract')
                                    ->label('Abstrak/Ringkasan')
                                    ->rows(5)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),

                        // Tab 6: Gambar & File
                        Forms\Components\Tabs\Tab::make('Gambar & File')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Gambar Sampul')
                                    ->image()
                                    ->disk('public')
                                    ->directory('covers')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->helperText('Upload gambar sampul buku (max 2MB, JPG/PNG/WebP)'),
                            ]),

                        // Tab 7: Pengaturan OPAC
                        Forms\Components\Tabs\Tab::make('Pengaturan')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Toggle::make('is_opac_visible')
                                    ->label('Tampilkan di OPAC')
                                    ->default(true)
                                    ->helperText('Tampilkan bibliografi ini di OPAC publik'),
                                Forms\Components\Toggle::make('opac_hide')
                                    ->label('Sembunyikan dari OPAC')
                                    ->helperText('Sembunyikan sementara dari OPAC'),
                                Forms\Components\Toggle::make('promoted')
                                    ->label('Promosikan')
                                    ->helperText('Tampilkan di halaman utama OPAC'),
                                Forms\Components\TextInput::make('labels')
                                    ->label('Label')
                                    ->helperText('Label tambahan, pisahkan dengan koma'),
                            ])->columns(4),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $this->save();
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['user_id'] = auth()->id();
        
        $itemQty = $data['item_qty'] ?? 1;
        unset($data['item_qty']);
        
        // Extract relations
        $authors = $data['authors'] ?? [];
        $subjects = $data['subjects'] ?? [];
        unset($data['authors'], $data['subjects']);

        if ($this->book) {
            $this->book->update($data);
            $this->book->authors()->sync($authors);
            $this->book->subjects()->sync($subjects);
            $message = 'Bibliografi berhasil diperbarui.';
        } else {
            $book = Book::create($data);
            $book->authors()->sync($authors);
            $book->subjects()->sync($subjects);
            
            // Get default location for branch
            $defaultLocation = \App\Models\Location::where('branch_id', $data['branch_id'])->first();
            
            // Get default item status (Tersedia)
            $defaultStatus = \App\Models\ItemStatus::where('name', 'like', '%Tersedia%')->first()
                ?? \App\Models\ItemStatus::first();
            
            // Generate items
            for ($i = 0; $i < $itemQty; $i++) {
                $book->items()->create([
                    'branch_id' => $data['branch_id'],
                    'call_number' => $data['call_number'] ?? null,
                    'location_id' => $defaultLocation?->id,
                    'item_status_id' => $defaultStatus?->id,
                    'source' => 'purchase',
                    'barcode' => 'B' . now()->format('ymd') . rand(1000, 9999),
                    'inventory_code' => 'ITM' . Str::random(5),
                ]);
            }
            
            $message = "Bibliografi berhasil ditambahkan dengan {$itemQty} eksemplar.";
        }

        Notification::make()
            ->title($message)
            ->success()
            ->send();

        $this->redirect(route('staff.biblio.index'));
    }

    public function render(): View
    {
        return view('livewire.staff.biblio.biblio-form')
            ->extends('staff.layouts.app')
            ->section('content');
    }
}
