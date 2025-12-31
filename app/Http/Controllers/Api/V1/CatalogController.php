<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogController extends BaseController
{
    public function index(Request $request)
    {
        $query = Book::with(['authors', 'publisher', 'items' => fn($q) => $q->where('is_available', true)])
            ->withCount(['items', 'items as available_items_count' => fn($q) => $q->where('is_available', true)]);

        // Search
        if ($q = $request->q) {
            $query->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('isbn', 'like', "%{$q}%")
                    ->orWhere('call_number', 'like', "%{$q}%")
                    ->orWhereHas('authors', fn($aq) => $aq->where('name', 'like', "%{$q}%"));
            });
        }

        // Filters
        if ($request->author_id) {
            $query->whereHas('authors', fn($q) => $q->where('authors.id', $request->author_id));
        }
        if ($request->subject_id) {
            $query->whereHas('subjects', fn($q) => $q->where('subjects.id', $request->subject_id));
        }
        if ($request->publisher_id) {
            $query->where('publisher_id', $request->publisher_id);
        }
        if ($request->year) {
            $query->where('year', $request->year);
        }
        if ($request->branch_id) {
            $query->whereHas('items', fn($q) => $q->where('branch_id', $request->branch_id));
        }
        if ($request->boolean('available')) {
            $query->whereHas('items', fn($q) => $q->where('is_available', true));
        }

        // Sort
        $query->when($request->sort, function ($q, $sort) {
            return match ($sort) {
                'title' => $q->orderBy('title'),
                'year' => $q->orderByDesc('year'),
                'newest' => $q->orderByDesc('created_at'),
                default => $q->orderByDesc('id'),
            };
        }, fn($q) => $q->orderByDesc('id'));

        $perPage = min($request->per_page ?? 20, 50);
        $books = $query->paginate($perPage);

        return $this->paginated($books->through(fn($book) => $this->formatBook($book)));
    }

    public function show($id)
    {
        $book = Book::with([
            'authors', 'publisher', 'place', 'subjects',
            'items' => fn($q) => $q->with(['location', 'itemStatus', 'branch']),
        ])->withCount(['items', 'items as available_items_count' => fn($q) => $q->where('is_available', true)])
            ->find($id);

        if (!$book) {
            return $this->error('Buku tidak ditemukan', 404);
        }

        return $this->success($this->formatBookDetail($book));
    }

    public function findByIsbn($isbn)
    {
        $book = Book::with(['authors', 'publisher', 'items'])
            ->where('isbn', $isbn)
            ->first();

        if (!$book) {
            return $this->error('Buku dengan ISBN tersebut tidak ditemukan', 404);
        }

        return $this->show($book->id);
    }

    public function filters()
    {
        return $this->success([
            'authors' => \App\Models\Author::withCount('books')->orderByDesc('books_count')->limit(100)->get(['id', 'name', 'books_count']),
            'subjects' => \App\Models\Subject::withCount('books')->orderByDesc('books_count')->limit(50)->get(['id', 'name', 'books_count']),
            'publishers' => \App\Models\Publisher::withCount('books')->orderByDesc('books_count')->limit(50)->get(['id', 'name', 'books_count']),
            'years' => Book::distinct()->orderByDesc('year')->limit(20)->pluck('year')->filter(),
            'branches' => \App\Models\Branch::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function popular(Request $request)
    {
        $books = Book::with(['authors', 'publisher'])
            ->withCount(['items', 'items as available_items_count' => fn($q) => $q->where('is_available', true)])
            ->withCount('loans')
            ->orderByDesc('loans_count')
            ->limit($request->limit ?? 10)
            ->get();

        return $this->success($books->map(fn($book) => $this->formatBook($book)));
    }

    public function newArrivals(Request $request)
    {
        $books = Book::with(['authors', 'publisher'])
            ->withCount(['items', 'items as available_items_count' => fn($q) => $q->where('is_available', true)])
            ->orderByDesc('created_at')
            ->limit($request->limit ?? 10)
            ->get();

        return $this->success($books->map(fn($book) => $this->formatBook($book)));
    }

    protected function formatBook(Book $book): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'authors' => $book->authors->pluck('name'),
            'publisher' => $book->publisher?->name,
            'year' => $book->year,
            'isbn' => $book->isbn,
            'cover_url' => $book->cover ? Storage::disk('public')->url($book->cover) : null,
            'call_number' => $book->call_number,
            'total_items' => $book->items_count ?? 0,
            'available_items' => $book->available_items_count ?? 0,
        ];
    }

    protected function formatBookDetail(Book $book): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'authors' => $book->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
            'publisher' => $book->publisher ? ['id' => $book->publisher->id, 'name' => $book->publisher->name] : null,
            'place' => $book->place?->name,
            'year' => $book->year,
            'edition' => $book->edition,
            'isbn' => $book->isbn,
            'pages' => $book->pages,
            'language' => $book->language,
            'cover_url' => $book->cover ? Storage::disk('public')->url($book->cover) : null,
            'call_number' => $book->call_number,
            'subjects' => $book->subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name]),
            'abstract' => $book->abstract,
            'table_of_contents' => $book->table_of_contents,
            'items' => $book->items->map(fn($item) => [
                'id' => $item->id,
                'barcode' => $item->barcode,
                'call_number' => $item->call_number,
                'location' => $item->location ? ['id' => $item->location->id, 'name' => $item->location->name] : null,
                'status' => $item->is_available ? 'available' : 'borrowed',
                'branch' => $item->branch ? ['id' => $item->branch->id, 'name' => $item->branch->name] : null,
            ]),
            'total_items' => $book->items_count,
            'available_items' => $book->available_items_count,
        ];
    }
}
