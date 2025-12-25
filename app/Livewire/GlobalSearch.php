<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Branch;
use App\Models\CollectionType;
use App\Models\Department;
use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\Faculty;
use App\Models\News;
use App\Models\Subject;
use App\Models\JournalSource;
use App\Services\OpenLibraryService;
use App\Services\ShamelaService;
use App\Services\KubukuService;
use Livewire\Component;
use Illuminate\Support\Collection;

class GlobalSearch extends Component
{

    // Search
    public string $query = '';
    
    // Primary Filters
    public string $resourceType = 'all';
    public $branchId = null;
    
    // Advanced Filters
    public array $selectedSubjects = [];
    public $collectionTypeId = null;
    public $facultyId = null;
    public $departmentId = null;
    public ?string $language = null;
    public $yearFrom = null;
    public $yearTo = null;
    public string $thesisType = '';
    public ?string $journalCode = null;
    public ?string $ebookSource = null;
    
    // Sort & View
    public string $sortBy = 'relevance';
    public string $viewMode = 'grid';
    
    // Pagination
    public int $page = 1;
    public int $perPage = 15;
    
    // UI State
    public bool $showMobileFilters = false;
    public bool $showAdvancedFilters = false;

    protected $queryString = [
        'query' => ['as' => 'q', 'except' => ''],
        'resourceType' => ['as' => 'type', 'except' => 'all'],
        'branchId' => ['as' => 'branch', 'except' => null],
        'journalCode' => ['as' => 'journal', 'except' => null],
        'ebookSource' => ['as' => 'esrc', 'except' => null],
        'collectionTypeId' => ['as' => 'collection', 'except' => null],
        'facultyId' => ['as' => 'faculty', 'except' => null],
        'departmentId' => ['as' => 'dept', 'except' => null],
        'language' => ['as' => 'lang', 'except' => null],
        'yearFrom' => ['as' => 'from', 'except' => null],
        'yearTo' => ['as' => 'to', 'except' => null],
        'thesisType' => ['as' => 'thesis', 'except' => ''],
        'sortBy' => ['as' => 'sort', 'except' => 'relevance'],
        'page' => ['except' => 1],
    ];

    protected $listeners = ['search' => 'performSearch'];

    public function mount()
    {
        $this->query = $this->sanitizeInput(request('q', ''));
        
        // Convert empty strings to null for optional params
        if ($this->branchId === '') $this->branchId = null;
        if ($this->collectionTypeId === '') $this->collectionTypeId = null;
        if ($this->facultyId === '') $this->facultyId = null;
        if ($this->departmentId === '') $this->departmentId = null;
        if ($this->yearFrom === '') $this->yearFrom = null;
        if ($this->yearTo === '') $this->yearTo = null;
        if ($this->language === '') $this->language = null;
    }

    public function updatingQuery($value)
    {
        $this->query = $this->sanitizeInput($value);
        $this->page = 1;
    }

    /**
     * Sanitize search input to prevent SQL wildcard injection
     */
    protected function sanitizeInput(string $value): string
    {
        $value = strip_tags($value);
        // Escape SQL LIKE wildcards to prevent ReDoS-like attacks
        return str_replace(['%', '_'], ['\\%', '\\_'], $value);
    }

    /**
     * Get sanitized search term for LIKE queries
     */
    protected function getSearchTerm(): string
    {
        return $this->query;
    }

    public function updatedResourceType()
    {
        $this->resetPage();
        // Reset type-specific filters
        if ($this->resourceType !== 'ethesis') {
            $this->facultyId = null;
            $this->departmentId = null;
            $this->thesisType = '';
        }
    }

    public function updatedFacultyId()
    {
        $this->departmentId = null;
        $this->page = 1;
    }

    public function setResourceType(string $type)
    {
        $this->resourceType = $type;
        $this->page = 1;
    }

    public function clearAllFilters()
    {
        $this->reset([
            'branchId', 'selectedSubjects', 'collectionTypeId', 
            'facultyId', 'departmentId', 'language', 
            'yearFrom', 'yearTo', 'thesisType', 'sortBy', 'journalCode', 'ebookSource'
        ]);
        $this->page = 1;
    }

    public function toggleSubject(int $subjectId)
    {
        if (in_array($subjectId, $this->selectedSubjects)) {
            $this->selectedSubjects = array_diff($this->selectedSubjects, [$subjectId]);
        } else {
            $this->selectedSubjects[] = $subjectId;
        }
        $this->page = 1;
    }

    public function removeSubject(int $subjectId)
    {
        $this->selectedSubjects = array_diff($this->selectedSubjects, [$subjectId]);
        $this->page = 1;
    }

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function nextPage()
    {
        if ($this->page < $this->totalPages) {
            $this->page++;
        }
    }

    public function gotoPage(int $page)
    {
        $this->page = max(1, min($page, $this->totalPages));
    }

    // Computed: Results
    public function getResultsProperty(): Collection
    {
        $results = collect();

        if ($this->resourceType === 'all' || $this->resourceType === 'book') {
            $results = $results->merge($this->searchBooks());
        }

        if ($this->resourceType === 'all' || $this->resourceType === 'ebook') {
            // Filter by source if specified
            if (!$this->ebookSource || $this->ebookSource === 'local') {
                $results = $results->merge($this->searchEbooks());
            }
            // KUBUKU e-books
            if (!$this->ebookSource || $this->ebookSource === 'kubuku') {
                $results = $results->merge($this->searchKubuku());
            }
        }

        if ($this->resourceType === 'all' || $this->resourceType === 'ethesis') {
            $results = $results->merge($this->searchEtheses());
        }

        if ($this->resourceType === 'all' || $this->resourceType === 'journal') {
            $results = $results->merge($this->searchJournals());
        }

        // External Sources (Open Library)
        if (($this->resourceType === 'all' || $this->resourceType === 'external') && $this->query) {
            $results = $results->merge($this->searchExternal());
        }

        // Shamela (Islamic Books)
        if (($this->resourceType === 'all' || $this->resourceType === 'shamela') && $this->query) {
            $results = $results->merge($this->searchShamela());
        }

        // Apply sorting then paginate
        $sorted = $this->applySorting($results);
        return $sorted->slice(($this->page - 1) * $this->perPage, $this->perPage)->values();
    }

    public function getTotalResultsProperty(): int
    {
        return $this->counts[$this->resourceType] ?? 0;
    }

    public function getTotalPagesProperty(): int
    {
        return max(1, ceil($this->totalResults / $this->perPage));
    }

    protected function searchBooks(): Collection
    {
        // Build base query
        $query = Book::query()
            ->withoutGlobalScopes()
            ->with(['authors', 'publisher', 'subjects', 'items.branch'])
            ->withCount('items')
            ->where(function($q) {
                $q->where('is_opac_visible', true)
                  ->orWhereNull('is_opac_visible');
            });

        // Use Meilisearch if search query exists
        if ($this->query) {
            $searchBuilder = Book::search($this->query);
            
            // Apply Meilisearch filters
            if ($this->branchId) {
                $searchBuilder->where('branch_id', $this->branchId);
            }
            if ($this->language) {
                $searchBuilder->where('language', $this->language);
            }
            if ($this->yearFrom) {
                $searchBuilder->where('year', '>=', (int) $this->yearFrom);
            }
            if ($this->yearTo) {
                $searchBuilder->where('year', '<=', (int) $this->yearTo);
            }
            
            // Get IDs from Meilisearch
            $bookIds = $searchBuilder->take(200)->keys()->toArray();
            
            if (empty($bookIds)) {
                return collect();
            }
            
            $query->whereIn('id', $bookIds);
        } else {
            // No search query - apply DB filters directly
            if ($this->branchId) {
                $query->where('branch_id', $this->branchId);
            }
            if ($this->language) {
                $query->where('language', $this->language);
            }
            if ($this->yearFrom) {
                $query->where('publish_year', '>=', $this->yearFrom);
            }
            if ($this->yearTo) {
                $query->where('publish_year', '<=', $this->yearTo);
            }
        }

        // Subject filter (always via DB - many-to-many relation)
        if (!empty($this->selectedSubjects)) {
            $query->whereHas('subjects', fn($s) => $s->whereIn('subjects.id', $this->selectedSubjects));
        }

        // Collection type filter (via items relation)
        if ($this->collectionTypeId) {
            $query->whereHas('items', fn($i) => $i->where('collection_type_id', $this->collectionTypeId));
        }

        return $query->limit(500)->get()->map(fn($book) => [
            'type' => 'book',
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author_names ?: '-',
            'cover' => $book->cover_url,
            'year' => $book->publish_year,
            'publisher' => $book->publisher?->name,
            'badge' => $book->items_count . ' eksemplar',
            'badgeColor' => 'blue',
            'icon' => 'fa-book',
            'url' => route('opac.catalog.show', $book->id),
            'description' => $book->abstract ? \Str::limit(strip_tags($book->abstract), 120) : null,
            'meta' => [
                'isbn' => $book->isbn,
                'call_number' => $book->call_number,
                'branch' => $book->items->first()?->branch?->name,
                'branches' => $book->items->pluck('branch.name')->unique()->filter()->values()->toArray(),
            ],
        ]);
    }

    protected function searchEbooks(): Collection
    {
        $query = Ebook::query()->where('is_active', true)->with('authors');

        if ($this->query) {
            $searchTerm = $this->query;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('abstract', 'like', "%{$searchTerm}%")
                  ->orWhereHas('authors', fn($a) => $a->where('name', 'like', "%{$searchTerm}%"));
            });
        }

        if ($this->yearFrom) {
            $query->where('publish_year', '>=', $this->yearFrom);
        }
        if ($this->yearTo) {
            $query->where('publish_year', '<=', $this->yearTo);
        }

        if ($this->language) {
            $query->where('language', $this->language);
        }

        return $query->limit(500)->get()->map(fn($ebook) => [
            'type' => 'ebook',
            'id' => $ebook->id,
            'title' => $ebook->title,
            'author' => $ebook->author_names ?: '-',
            'cover' => $ebook->cover_url,
            'year' => $ebook->publish_year,
            'badge' => strtoupper($ebook->file_format ?? 'PDF'),
            'badgeColor' => 'orange',
            'icon' => 'fa-file-pdf',
            'url' => route('opac.ebook.show', $ebook->id),
            'description' => $ebook->abstract ? \Str::limit(strip_tags($ebook->abstract), 120) : null,
            'meta' => [
                'format' => $ebook->file_format,
                'access' => $ebook->access_type,
            ],
        ]);
    }

    protected function searchEtheses(): Collection
    {
        $query = Ethesis::query()
            ->where('is_public', true)
            ->with('department.faculty');

        if ($this->query) {
            $searchTerm = $this->query;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('title_en', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('nim', 'like', "%{$searchTerm}%")
                  ->orWhere('abstract', 'like', "%{$searchTerm}%")
                  ->orWhere('keywords', 'like', "%{$searchTerm}%");
            });
        }

        if ($this->facultyId) {
            $query->whereHas('department', fn($d) => $d->where('faculty_id', $this->facultyId));
        }

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        if ($this->thesisType) {
            $query->where('type', $this->thesisType);
        }

        if ($this->yearFrom) {
            $query->where('year', '>=', $this->yearFrom);
        }
        if ($this->yearTo) {
            $query->where('year', '<=', $this->yearTo);
        }

        return $query->limit(500)->get()->map(fn($thesis) => [
            'type' => 'ethesis',
            'id' => $thesis->id,
            'title' => $thesis->title,
            'author' => $thesis->author,
            'cover' => $thesis->cover_url,
            'year' => $thesis->year,
            'badge' => $thesis->source_type === 'repo' ? 'Repo' : $thesis->getTypeLabel(),
            'badgeColor' => $thesis->source_type === 'repo' ? 'indigo' : 'purple',
            'icon' => 'fa-graduation-cap',
            'url' => route('opac.ethesis.show', $thesis->id),
            'description' => $thesis->abstract ? \Str::limit(strip_tags($thesis->abstract), 120) : null,
            'meta' => [
                'department' => $thesis->department?->name,
                'faculty' => $thesis->department?->faculty?->name,
                'nim' => $thesis->nim,
                'source' => $thesis->source_type,
            ],
        ]);
    }

    protected function searchNews(): Collection
    {
        $query = News::query()
            ->published()
            ->with('category');

        if ($this->query) {
            $searchTerm = $this->query;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        if ($this->yearFrom) {
            $query->whereYear('published_at', '>=', $this->yearFrom);
        }
        if ($this->yearTo) {
            $query->whereYear('published_at', '<=', $this->yearTo);
        }

        return $query->limit(50)->get()->map(fn($news) => [
            'type' => 'news',
            'id' => $news->id,
            'title' => $news->title,
            'author' => $news->author?->name ?? 'Admin',
            'cover' => $news->image_url,
            'year' => $news->published_at?->format('d M Y'),
            'badge' => $news->category?->name ?? 'Berita',
            'badgeColor' => 'green',
            'icon' => 'fa-newspaper',
            'url' => route('opac.news.show', $news->slug),
            'description' => $news->excerpt ? \Str::limit(strip_tags($news->excerpt), 120) : null,
            'meta' => [
                'views' => $news->views,
            ],
        ]);
    }

    protected function searchJournals(): Collection
    {
        $query = \App\Models\JournalArticle::query()->with('source');

        if ($this->query) {
            $searchTerm = $this->query;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('abstract', 'like', "%{$searchTerm}%")
                  ->orWhereRaw("JSON_SEARCH(authors, 'one', ?) IS NOT NULL", ["%{$searchTerm}%"]);
            });
        }

        if ($this->journalCode) {
            $query->whereHas('source', fn($q) => $q->where('code', $this->journalCode));
        }

        if ($this->yearFrom) {
            $query->where('publish_year', '>=', $this->yearFrom);
        }
        if ($this->yearTo) {
            $query->where('publish_year', '<=', $this->yearTo);
        }

        return $query->orderByDesc('published_at')->limit(500)->get()->map(fn($article) => [
            'type' => 'journal',
            'id' => $article->id,
            'title' => $article->title,
            'author' => $article->authors_string ?: '-',
            'cover' => $article->cover_url,
            'year' => $article->publish_year,
            'publisher' => $article->journal_name,
            'badge' => $article->source_type === 'repo' ? 'Repo' : ($article->source?->name ?? 'Jurnal'),
            'badgeColor' => $article->source_type === 'repo' ? 'indigo' : 'purple',
            'icon' => 'fa-file-lines',
            'url' => route('opac.journals.show', $article->id),
            'description' => $article->abstract ? \Str::limit(strip_tags($article->abstract), 150) : null,
            'meta' => [
                'journal' => $article->journal_name,
                'doi' => $article->doi,
                'source' => $article->source_type,
            ],
        ]);
    }

    protected function searchExternal(): Collection
    {
        if (empty($this->query)) {
            return collect();
        }
        
        $openLibrary = app(OpenLibraryService::class);
        
        // When showing only external, use pagination with offset
        if ($this->resourceType === 'external') {
            $offset = ($this->page - 1) * $this->perPage;
            return $openLibrary->search($this->query, $this->perPage, $offset);
        }
        
        // When showing 'all', just return limited results
        return $openLibrary->search($this->query);
    }

    protected function searchShamela(): Collection
{
    if (empty($this->query)) {
        return collect();
    }
    
    $shamela = app(ShamelaService::class);
    $books = $shamela->search($this->query, 24);
    
    return $books->map(fn($book) => [
        'type' => 'shamela',
        'id' => $book['id'],
        'title' => $book['title'],
        'author' => $book['author'] ?? 'المكتبة الشاملة',
        'cover' => $book['cover'],
        'year' => $book['hijri_year'] ?? null,
        'publisher' => $book['category'] ?? 'Shamela',
        'badge' => ($book['has_pdf'] ?? false) ? 'PDF متاح' : 'كتاب إسلامي',
        'badgeColor' => ($book['has_pdf'] ?? false) ? 'rose' : 'emerald',
        'icon' => 'fa-book-quran',
        'url' => route('opac.shamela.show', $book['id']),
        'description' => $book['author'] ? 'وفاة: ' . ($book['author_death'] ?? '-') . ' هـ' : null,
        'meta' => [
            'source' => $book['source'] ?? 'shamela',
            'external_url' => $book['url'],
            'has_pdf' => $book['has_pdf'] ?? false,
            'pdf_links' => $book['pdf_links'] ?? [],
        ],
    ]);
}

    protected function searchKubuku(): Collection
    {
        $kubuku = app(KubukuService::class);
        
        if (!$kubuku->isEnabled()) {
            return collect();
        }

        // If no query, get all books (paginated)
        if (empty($this->query)) {
            if ($this->resourceType === 'ebook' && $this->ebookSource === 'kubuku') {
                return $kubuku->getAll($this->page);
            }
            return collect();
        }

        // When showing only ebook, use pagination
        if ($this->resourceType === 'ebook') {
            return $kubuku->search($this->query, $this->page);
        }

        // When showing 'all', just return first page
        return $kubuku->search($this->query, 1)->take(10);
    }

    protected function applySorting(Collection $results): Collection
    {
        return match($this->sortBy) {
            'title_asc' => $results->sortBy('title'),
            'title_desc' => $results->sortByDesc('title'),
            'newest' => $results->sortByDesc('year'),
            'oldest' => $results->sortBy('year'),
            default => $results,
        };
    }

    // Computed: Counts
    public function getCountsProperty(): array
    {
        $baseQuery = $this->query;
        $externalCount = $this->getExternalCount($baseQuery);
        $shamelaCount = $this->getShamelaCount($baseQuery);
        
        $kubukuCount = $this->getKubukuCount($baseQuery);
        
        return [
            'all' => $this->getBookCount($baseQuery) + $this->getEbookCount($baseQuery) + $kubukuCount +
                     $this->getEthesisCount($baseQuery) +
                     $this->getJournalCount($baseQuery) + $externalCount + $shamelaCount,
            'book' => $this->getBookCount($baseQuery),
            'ebook' => $this->getEbookCount($baseQuery) + $kubukuCount,
            'ethesis' => $this->getEthesisCount($baseQuery),
            'journal' => $this->getJournalCount($baseQuery),
            'external' => $externalCount,
            'shamela' => $shamelaCount,
        ];
    }

    protected function getBookCount(?string $search): int
    {
        if ($search) {
            try {
                $searchBuilder = Book::search($search);
                
                // Apply same filters as searchBooks
                if ($this->branchId) {
                    $searchBuilder->where('branch_id', $this->branchId);
                }
                if ($this->language) {
                    $searchBuilder->where('language', $this->language);
                }
                if ($this->yearFrom) {
                    $searchBuilder->where('year', '>=', (int) $this->yearFrom);
                }
                if ($this->yearTo) {
                    $searchBuilder->where('year', '<=', (int) $this->yearTo);
                }
                
                $result = $searchBuilder->raw();
                return $result['estimatedTotalHits'] ?? 0;
            } catch (\Exception $e) {
                return Book::withoutGlobalScopes()->where('title', 'like', "%{$search}%")->count();
            }
        }
        
        // No search - count from DB with filters
        $query = Book::withoutGlobalScopes();
        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }
        if ($this->language) {
            $query->where('language', $this->language);
        }
        if ($this->yearFrom) {
            $query->where('publish_year', '>=', $this->yearFrom);
        }
        if ($this->yearTo) {
            $query->where('publish_year', '<=', $this->yearTo);
        }
        return $query->count();
    }

    protected function getEbookCount(?string $search): int
    {
        $query = Ebook::where('is_active', true);
        if ($search) {
            $query->where(fn($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhereHas('authors', fn($a) => $a->where('name', 'like', "%{$search}%")));
        }
        return $query->count();
    }

    protected function getEthesisCount(?string $search): int
    {
        $query = Ethesis::where('is_public', true);
        if ($search) {
            $query->where(fn($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%"));
        }
        return $query->count();
    }

    protected function getNewsCount(?string $search): int
    {
        $query = News::published();
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }
        return $query->count();
    }

    protected function getJournalCount(?string $search): int
    {
        $query = \App\Models\JournalArticle::query();
        if ($search) {
            $query->where(fn($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('abstract', 'like', "%{$search}%"));
        }
        return $query->count();
    }

    protected function getExternalCount(?string $search): int
{
    $openLibrary = app(OpenLibraryService::class);
    if (!$openLibrary->isEnabled()) {
        return 0;
    }
    
    // If no search query, return estimated total (Open Library has millions)
    if (empty($search)) {
        return 2000000; // Estimated 2M+ public domain books
    }
    
    // Get actual count from API (cached)
    return $openLibrary->getSearchCount($search);
}

    protected function getShamelaCount(?string $search): int
{
    // Use local database for accurate count
    $localService = new \App\Services\ShamelaLocalService();
    
    if (!$localService->isAvailable()) {
        return 0;
    }
    
    // If no search query, return total books from local database
    if (empty($search)) {
        $stats = $localService->getStats();
        return $stats['total_books'] ?? 0;
    }
    
    // Search and return actual result count
    $result = $localService->search($search, 100);
    return $result['total'] ?? 0;
}

    protected function getKubukuCount(?string $search): int
    {
        $kubuku = app(KubukuService::class);
        
        if (!$kubuku->isEnabled()) {
            return 0;
        }
        
        // If no search query, return total from API
        if (empty($search)) {
            return $kubuku->getTotalCount();
        }
        
        // Get search count
        return $kubuku->getSearchCount($search);
    }

    // Computed: Filter Options
    public function getBranchesProperty()
    {
        return Branch::where('is_active', true)->orderBy('name')->get();
    }

    public function getSubjectsProperty()
    {
        return Subject::orderBy('name')->limit(100)->get();
    }

    public function getPopularSubjectsProperty()
    {
        return Subject::withCount('books')
            ->orderByDesc('books_count')
            ->limit(10)
            ->get();
    }

    public function getCollectionTypesProperty()
    {
        return CollectionType::orderBy('name')->get();
    }

    public function getFacultiesProperty()
    {
        return Faculty::orderBy('name')->get();
    }

    public function getDepartmentsProperty()
    {
        if ($this->facultyId) {
            return Department::where('faculty_id', $this->facultyId)->orderBy('name')->get();
        }
        return Department::orderBy('name')->get();
    }

    public function getLanguagesProperty(): array
    {
        return [
            'id' => 'Indonesia',
            'en' => 'English',
            'ar' => 'Arabic',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
        ];
    }

    public function getJournalSourcesProperty()
    {
        return JournalSource::where('is_active', true)->orderBy('name')->get();
    }

    public function getActiveFiltersCountProperty(): int
    {
        $count = 0;
        if ($this->branchId) $count++;
        if (!empty($this->selectedSubjects)) $count += count($this->selectedSubjects);
        if ($this->collectionTypeId) $count++;
        if ($this->facultyId) $count++;
        if ($this->departmentId) $count++;
        if ($this->language) $count++;
        if ($this->yearFrom) $count++;
        if ($this->yearTo) $count++;
        if ($this->thesisType) $count++;
        if ($this->journalCode) $count++;
        return $count;
    }

    public function render()
    {
        return view('livewire.global-search', [
            'results' => $this->results,
            'counts' => $this->counts,
        ]);
    }
}
