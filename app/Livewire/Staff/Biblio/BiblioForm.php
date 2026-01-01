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

    // Master data (preloaded)
    public $branches = [];
    public $locations = [];
    public $mediaTypes = [];
    public $contentTypes = [];
    public $frequencies = [];
    public $places = [];
    public $languages = [];

    protected function rules(): array
    {
        return [
            'title' => 'required|min:3|max:500',
            'branch_id' => 'required|exists:branches,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi',
            'title.min' => 'Judul minimal 3 karakter',
            'branch_id.required' => 'Lokasi/cabang wajib dipilih',
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
        $this->places = Place::orderBy('name')->pluck('name', 'id')->toArray();
        
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
            ],
            2 => [], // Authors/subjects optional
            3 => [], // Publishing info optional
            4 => [
                'cover_image' => 'nullable|image|max:2048',
            ],
            default => [],
        };

        if (!empty($rules)) {
            $this->validate($rules, $this->messages());
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

        // Handle cover upload
        if ($this->cover_image) {
            $data['image'] = $this->cover_image->store('covers', 'public');
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
