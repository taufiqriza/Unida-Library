<?php

namespace App\Livewire\Staff\Biblio;

use App\Models\Book;
use App\Models\Author;
use App\Models\Subject;
use App\Models\Publisher;
use App\Models\Place;
use App\Models\Branch;
use App\Models\Location;
use App\Models\ItemStatus;
use App\Models\MediaType;
use App\Models\ContentType;
use App\Models\Frequency;
use App\Services\CallNumberService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class BiblioForm extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 4;
    
    public ?Book $book = null;
    public bool $isEdit = false;

    // Step 1: Info Utama
    public ?int $branch_id = null;
    public ?int $location_id = null;
    public string $title = '';
    public ?string $edition = null;
    public ?string $spec_detail_info = null;
    public ?int $media_type_id = null;
    public ?int $content_type_id = null;
    public int $item_qty = 1;

    // Step 2: Penulis & Subjek
    public array $selectedAuthors = [];
    public array $selectedSubjects = [];
    public string $authorSearch = '';
    public string $subjectSearch = '';
    public array $authorResults = [];
    public array $subjectResults = [];

    // Step 3: Penerbitan & Klasifikasi
    public ?int $publisher_id = null;
    public ?int $place_id = null;
    public ?string $publish_year = null;
    public ?string $collation = null;
    public ?string $isbn = null;
    public string $language = 'id';
    public ?string $classification = null;
    public ?string $call_number = null;
    public ?string $series_title = null;
    public ?int $frequency_id = null;

    // Step 4: Detail & File
    public ?string $abstract = null;
    public ?string $notes = null;
    public $cover_image = null;
    public bool $is_opac_visible = true;
    public bool $promoted = false;

    // Cover Finder
    public string $coverUrl = '';
    public array $coverResults = [];

    // Copy Catalog
    public bool $showCopyModal = false;
    public string $copySearch = '';
    public array $copyResults = [];

    // Master data (preloaded)
    public $branches = [];
    public $locations = [];
    public $mediaTypes = [];
    public $contentTypes = [];
    public $frequencies = [];
    public $languages = [];

    protected function rules(): array
    {
        return [
            'title' => 'required|min:3|max:500',
            'branch_id' => 'required|exists:branches,id',
            'location_id' => 'required|exists:locations,id',
            'selectedAuthors' => 'required|array|min:1',
            'classification' => 'required|min:1',
            'call_number' => 'required|min:3',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi',
            'title.min' => 'Judul minimal 3 karakter',
            'branch_id.required' => 'Cabang wajib dipilih',
            'location_id.required' => 'Lokasi wajib dipilih',
            'selectedAuthors.required' => 'Minimal 1 penulis wajib dipilih',
            'selectedAuthors.min' => 'Minimal 1 penulis wajib dipilih',
            'classification.required' => 'Klasifikasi DDC wajib diisi',
            'call_number.required' => 'Nomor panggil wajib diisi',
            'call_number.min' => 'Nomor panggil minimal 3 karakter',
        ];
    }

    public function mount($id = null): void
    {
        $this->loadMasterData();
        
        $user = auth()->user();
        $this->branch_id = $user->branch_id ?? Branch::first()?->id;
        $this->loadLocations();

        if ($id) {
            $this->book = Book::withoutGlobalScopes()->with(['authors', 'subjects', 'items'])->findOrFail($id);
            $this->isEdit = true;
            $this->loadBookData();
        }
    }

    protected function loadMasterData(): void
    {
        $user = auth()->user();
        
        // Branches - super_admin sees all
        if ($user->role === 'super_admin') {
            $this->branches = Branch::orderBy('name')->pluck('name', 'id')->toArray();
        } else {
            $this->branches = Branch::where('id', $user->branch_id)->pluck('name', 'id')->toArray();
        }
        
        $this->mediaTypes = MediaType::orderBy('name')->pluck('name', 'id')->toArray();
        $this->contentTypes = ContentType::orderBy('name')->pluck('name', 'id')->toArray();
        $this->frequencies = Frequency::orderBy('name')->pluck('name', 'id')->toArray();
        
        $this->languages = [
            'id' => 'Indonesia',
            'en' => 'English', 
            'ar' => 'Arabic',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
        ];
    }

    protected function loadBookData(): void
    {
        $b = $this->book;
        $this->branch_id = $b->branch_id;
        $this->title = $b->title;
        $this->edition = $b->edition;
        $this->spec_detail_info = $b->spec_detail_info;
        $this->media_type_id = $b->media_type_id;
        $this->content_type_id = $b->content_type_id;
        $this->publisher_id = $b->publisher_id;
        $this->place_id = $b->place_id;
        $this->publish_year = $b->publish_year;
        $this->collation = $b->collation;
        $this->isbn = $b->isbn;
        $this->language = $b->language ?? 'id';
        $this->classification = $b->classification;
        $this->call_number = $b->call_number;
        $this->series_title = $b->series_title;
        $this->frequency_id = $b->frequency_id;
        $this->abstract = $b->abstract;
        $this->notes = $b->notes;
        $this->is_opac_visible = $b->is_opac_visible ?? true;
        $this->promoted = $b->promoted ?? false;
        
        // Load relations
        $this->selectedAuthors = $b->authors->map(fn($a) => [
            'id' => $a->id, 
            'name' => $a->name
        ])->toArray();
        
        $this->selectedSubjects = $b->subjects->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name
        ])->toArray();
    }

    // Update locations when branch changes
    public function updatedBranchId($value): void
    {
        $this->loadLocations();
        $this->location_id = null;
    }

    protected function loadLocations(): void
    {
        $this->locations = $this->branch_id 
            ? Location::where('branch_id', $this->branch_id)->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
    }

    // Step Navigation
    public function nextStep(): void
    {
        if ($this->validateCurrentStep()) {
            if ($this->step < $this->totalSteps) {
                $this->step++;
            }
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step < $this->step) {
            $this->step = $step;
        }
    }

    protected function validateCurrentStep(): bool
    {
        $rules = match($this->step) {
            1 => [
                'title' => 'required|min:3|max:500',
                'branch_id' => 'required',
                'location_id' => 'required',
            ],
            2 => [
                'selectedAuthors' => 'required|array|min:1',
            ],
            3 => [
                'classification' => 'required|min:1',
                'call_number' => 'required|min:3',
            ],
            4 => [
                'cover_image' => 'nullable|image|max:2048',
            ],
            default => [],
        };

        $messages = [
            'title.required' => 'Judul wajib diisi',
            'title.min' => 'Judul minimal 3 karakter',
            'branch_id.required' => 'Cabang wajib dipilih',
            'location_id.required' => 'Lokasi wajib dipilih',
            'selectedAuthors.required' => 'Minimal 1 penulis wajib dipilih',
            'selectedAuthors.min' => 'Minimal 1 penulis wajib dipilih',
            'classification.required' => 'Klasifikasi DDC wajib diisi',
            'call_number.required' => 'Nomor panggil wajib diisi',
            'call_number.min' => 'Nomor panggil minimal 3 karakter',
        ];

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }

        return true;
    }

    // Auto-trigger search when typing
    public function updatedAuthorSearch(): void
    {
        $this->searchAuthors();
    }

    public function updatedSubjectSearch(): void
    {
        $this->searchSubjects();
    }

    // Author Search
    public function searchAuthors(): void
    {
        if (strlen($this->authorSearch) < 2) {
            $this->authorResults = [];
            return;
        }
        
        $this->authorResults = Author::where('name', 'like', "%{$this->authorSearch}%")
            ->limit(10)
            ->get(['id', 'name'])
            ->toArray();
    }

    public function addAuthor(int $id, string $name): void
    {
        if (!collect($this->selectedAuthors)->contains('id', $id)) {
            $this->selectedAuthors[] = ['id' => $id, 'name' => $name];
        }
        $this->authorSearch = '';
        $this->authorResults = [];
    }

    public function removeAuthor(int $id): void
    {
        $this->selectedAuthors = array_values(
            array_filter($this->selectedAuthors, fn($a) => $a['id'] !== $id)
        );
    }

    public function createAuthor(): void
    {
        if (strlen($this->authorSearch) < 2) return;
        
        $author = Author::create(['name' => $this->authorSearch, 'type' => 'personal']);
        $this->addAuthor($author->id, $author->name);
    }

    // Subject Search
    public function searchSubjects(): void
    {
        if (strlen($this->subjectSearch) < 2) {
            $this->subjectResults = [];
            return;
        }
        
        $this->subjectResults = Subject::where('name', 'like', "%{$this->subjectSearch}%")
            ->limit(10)
            ->get(['id', 'name'])
            ->toArray();
    }

    public function addSubject(int $id, string $name): void
    {
        if (!collect($this->selectedSubjects)->contains('id', $id)) {
            $this->selectedSubjects[] = ['id' => $id, 'name' => $name];
        }
        $this->subjectSearch = '';
        $this->subjectResults = [];
    }

    public function removeSubject(int $id): void
    {
        $this->selectedSubjects = array_values(
            array_filter($this->selectedSubjects, fn($s) => $s['id'] !== $id)
        );
    }

    public function createSubject(): void
    {
        if (strlen($this->subjectSearch) < 2) return;
        
        $subject = Subject::create(['name' => $this->subjectSearch, 'type' => 'topic']);
        $this->addSubject($subject->id, $subject->name);
    }

    // Publisher Search (via API)
    public function getPublisherName(): ?string
    {
        if (!$this->publisher_id) return null;
        return Publisher::find($this->publisher_id)?->name;
    }

    // Generate Call Number
    public function generateCallNumber(): void
    {
        $authorCode = '';
        if (!empty($this->selectedAuthors)) {
            $authorCode = CallNumberService::getAuthorCode($this->selectedAuthors[0]['name']);
        }
        
        $titleCode = CallNumberService::getTitleCode($this->title);
        
        $parts = array_filter(['S', $this->classification, $authorCode, $titleCode]);
        $this->call_number = implode(' ', $parts);
        
        $this->dispatch('notify', type: 'success', message: 'Nomor panggil berhasil di-generate');
    }

    // Apply DDC from modal
    public function applyDdc(string $code): void
    {
        $this->classification = $code;
    }

    // Save
    public function save(): void
    {
        $this->validate([
            'title' => 'required|min:3',
            'branch_id' => 'required',
        ]);

        $data = [
            'branch_id' => $this->branch_id,
            'user_id' => auth()->id(),
            'title' => $this->title,
            'edition' => $this->edition,
            'spec_detail_info' => $this->spec_detail_info,
            'media_type_id' => $this->media_type_id,
            'content_type_id' => $this->content_type_id,
            'publisher_id' => $this->publisher_id,
            'place_id' => $this->place_id,
            'publish_year' => $this->publish_year,
            'collation' => $this->collation,
            'isbn' => $this->isbn,
            'language' => $this->language,
            'classification' => $this->classification,
            'call_number' => $this->call_number,
            'series_title' => $this->series_title,
            'frequency_id' => $this->frequency_id,
            'abstract' => $this->abstract,
            'notes' => $this->notes,
            'is_opac_visible' => $this->is_opac_visible,
            'promoted' => $this->promoted,
        ];

        // Handle cover upload or downloaded cover
        if ($this->cover_image) {
            $data['image'] = $this->cover_image->store('covers', 'public');
        } elseif (session('pending_cover')) {
            $data['image'] = session()->pull('pending_cover');
        }

        $authorIds = collect($this->selectedAuthors)->pluck('id')->toArray();
        $subjectIds = collect($this->selectedSubjects)->pluck('id')->toArray();

        if ($this->isEdit) {
            $this->book->update($data);
            $this->book->authors()->sync($authorIds);
            $this->book->subjects()->sync($subjectIds);
            $message = 'Bibliografi berhasil diperbarui.';
        } else {
            $book = Book::create($data);
            $book->authors()->sync($authorIds);
            $book->subjects()->sync($subjectIds);
            
            // Create items
            $this->createItems($book);
            $message = "Bibliografi berhasil ditambahkan dengan {$this->item_qty} eksemplar.";
        }

        $this->dispatch('showSuccess', title: 'Berhasil!', message: $message);
        
        $this->redirect(route('staff.biblio.index'), navigate: true);
    }

    // Copy Catalog Methods
    public function openCopyModal(): void
    {
        $this->showCopyModal = true;
        $this->copySearch = '';
        $this->copyResults = [];
    }

    public function searchCatalog(): void
    {
        $this->copyResults = [];
        if (strlen($this->copySearch) < 3) return;

        $search = $this->copySearch;
        $this->copyResults = Book::query()
            ->select(['id', 'branch_id', 'title', 'isbn', 'image', 'call_number', 'classification', 'publish_year'])
            ->with(['branch:id,name', 'authors:id,name'])
            ->withCount('items')
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('authors', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->orderByDesc('items_count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function copyCatalog(int $bookId): void
    {
        $source = Book::with(['authors', 'subjects', 'publisher', 'place'])->find($bookId);
        if (!$source) return;

        // Copy basic fields
        $this->title = $source->title;
        $this->isbn = $source->isbn;
        $this->edition = $source->edition;
        $this->spec_detail_info = $source->spec_detail_info;
        $this->media_type_id = $source->media_type_id;
        $this->content_type_id = $source->content_type_id;
        $this->publisher_id = $source->publisher_id;
        $this->place_id = $source->place_id;
        $this->publish_year = $source->publish_year;
        $this->collation = $source->collation;
        $this->language = $source->language ?? 'id';
        $this->classification = $source->classification;
        $this->call_number = $source->call_number;
        $this->series_title = $source->series_title;
        $this->abstract = $source->abstract;
        $this->notes = $source->notes;

        // Copy authors
        $this->selectedAuthors = $source->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name])->toArray();

        // Copy subjects
        $this->selectedSubjects = $source->subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray();

        // Reference same cover image
        if ($source->image) {
            session(['pending_cover' => $source->image]);
        }

        $this->showCopyModal = false;
        $this->copySearch = '';
        $this->copyResults = [];

        $this->dispatch('notify', type: 'success', message: 'Data katalog berhasil disalin dari ' . $source->branch?->name);
    }

    // Cover Finder Methods
    public function searchCoverByIsbn(): void
    {
        $this->coverResults = [];
        if (empty($this->isbn)) return;
        
        $isbn = preg_replace('/[^0-9X]/', '', $this->isbn);
        
        try {
            // Google Books first (more reliable)
            $googleData = @file_get_contents("https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}", false, stream_context_create(['http' => ['timeout' => 5]]));
            if ($googleData) {
                $data = json_decode($googleData, true);
                if (!empty($data['items'][0]['volumeInfo']['imageLinks']['thumbnail'])) {
                    $thumb = str_replace(['http://', 'zoom=1'], ['https://', 'zoom=2'], $data['items'][0]['volumeInfo']['imageLinks']['thumbnail']);
                    $this->coverResults[] = ['url' => $thumb, 'source' => 'Google'];
                }
            }
            
            // Open Library - check if real image exists
            $olUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";
            $headers = @get_headers($olUrl, true);
            if ($headers && isset($headers['Content-Length']) && $headers['Content-Length'] > 1000) {
                $this->coverResults[] = ['url' => $olUrl, 'source' => 'OpenLibrary'];
            }
        } catch (\Exception $e) {}
    }

    public function searchCoverByTitle(): void
    {
        $this->coverResults = [];
        if (empty($this->title)) return;
        
        $query = urlencode($this->title);
        
        try {
            // Google Books by title - more results
            $googleData = @file_get_contents("https://www.googleapis.com/books/v1/volumes?q={$query}&maxResults=15", false, stream_context_create(['http' => ['timeout' => 5]]));
            if ($googleData) {
                $data = json_decode($googleData, true);
                foreach ($data['items'] ?? [] as $item) {
                    if (!empty($item['volumeInfo']['imageLinks']['thumbnail'])) {
                        $thumb = str_replace(['http://', 'zoom=1'], ['https://', 'zoom=2'], $item['volumeInfo']['imageLinks']['thumbnail']);
                        $this->coverResults[] = ['url' => $thumb, 'source' => 'Google'];
                    }
                }
            }
            
            // Open Library by title
            $olData = @file_get_contents("https://openlibrary.org/search.json?title=" . $query . "&limit=10", false, stream_context_create(['http' => ['timeout' => 5]]));
            if ($olData) {
                $data = json_decode($olData, true);
                foreach ($data['docs'] ?? [] as $doc) {
                    if (!empty($doc['cover_i'])) {
                        $this->coverResults[] = ['url' => "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-L.jpg", 'source' => 'OpenLibrary'];
                    }
                }
            }
        } catch (\Exception $e) {}
    }

    public function applyCoverFromUrl(string $url): void
    {
        try {
            $ctx = stream_context_create(['http' => ['timeout' => 10, 'user_agent' => 'Mozilla/5.0']]);
            $imageData = @file_get_contents($url, false, $ctx);
            
            if (!$imageData || strlen($imageData) < 1000) {
                $this->dispatch('notify', type: 'error', message: 'Gambar tidak valid atau terlalu kecil');
                return;
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($imageData);
            $ext = match($mime) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                default => null
            };

            if (!$ext) {
                $this->dispatch('notify', type: 'error', message: 'Format gambar tidak didukung');
                return;
            }

            $filename = 'covers/cover_' . Str::slug(substr($this->title, 0, 50)) . '-' . now()->format('YmdHis') . '.' . $ext;
            \Storage::disk('public')->put($filename, $imageData);

            // Update book if editing, or store for new
            if ($this->isEdit && $this->book) {
                $this->book->update(['image' => $filename]);
                $this->book->refresh();
            } else {
                $this->cover_image = null; // Clear uploaded file
                session(['pending_cover' => $filename]);
            }

            $this->coverUrl = '';
            $this->coverResults = [];
            $this->dispatch('notify', type: 'success', message: 'Cover berhasil diterapkan');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal: ' . $e->getMessage());
        }
    }

    public function downloadCoverFromUrl(): void
    {
        if (empty($this->coverUrl)) return;
        $this->applyCoverFromUrl($this->coverUrl);
    }

    protected function createItems(Book $book): void
    {
        // Use selected location or get default for branch
        $locationId = $this->location_id 
            ?? Location::where('branch_id', $this->branch_id)->first()?->id;
            
        $defaultStatus = ItemStatus::where('name', 'like', '%Tersedia%')->first() 
            ?? ItemStatus::first();

        // Get last inventory number for this branch today
        $today = now()->format('ymd');
        $lastItem = \App\Models\Item::where('inventory_code', 'like', "INV-{$this->branch_id}-{$today}-%")
            ->orderByDesc('inventory_code')
            ->first();
        $lastNum = $lastItem ? (int) substr($lastItem->inventory_code, -4) : 0;

        for ($i = 0; $i < $this->item_qty; $i++) {
            $lastNum++;
            $book->items()->create([
                'branch_id' => $this->branch_id,
                'call_number' => $this->call_number,
                'location_id' => $locationId,
                'item_status_id' => $defaultStatus?->id,
                'source' => 'purchase',
                'barcode' => 'B' . $today . str_pad($lastNum, 4, '0', STR_PAD_LEFT),
                'inventory_code' => "INV-{$this->branch_id}-{$today}-" . str_pad($lastNum, 4, '0', STR_PAD_LEFT),
            ]);
        }
    }

    public function getCompletionPercentageProperty(): int
    {
        $completed = 0;
        if ($this->title && $this->branch_id) $completed++;
        if (!empty($this->selectedAuthors)) $completed++;
        if ($this->classification || $this->call_number) $completed++;
        if ($this->is_opac_visible !== null) $completed++;
        return (int)(($completed / 4) * 100);
    }

    public function render()
    {
        return view('livewire.staff.biblio.biblio-form')
            ->extends('staff.layouts.app')
            ->section('content');
    }
}
