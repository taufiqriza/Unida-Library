<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Models\Subject;
use App\Models\Publisher;
use App\Models\MediaType;
use App\Models\CollectionType;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::withoutGlobalScopes()
            ->with(['authors', 'publisher', 'mediaType', 'collectionType'])
            ->withCount(['items', 'items as available_count' => fn($q) => $q->where('status', 'available')]);

        // Search
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('call_number', 'like', "%{$search}%")
                    ->orWhereHas('authors', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('subjects', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Filters
        if ($author = $request->input('author')) {
            $query->whereHas('authors', fn($q) => $q->where('authors.id', $author));
        }
        if ($subject = $request->input('subject')) {
            $query->whereHas('subjects', fn($q) => $q->where('subjects.id', $subject));
        }
        if ($publisher = $request->input('publisher')) {
            $query->where('publisher_id', $publisher);
        }
        if ($gmd = $request->input('gmd')) {
            $query->where('media_type_id', $gmd);
        }
        if ($collection = $request->input('collection')) {
            $query->where('collection_type_id', $collection);
        }
        if ($year = $request->input('year')) {
            $query->where('publish_year', $year);
        }
        if ($branch = $request->input('branch')) {
            $query->where('branch_id', $branch);
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'title' => $query->orderBy('title'),
            'year' => $query->orderByDesc('publish_year'),
            'popular' => $query->withCount('loans')->orderByDesc('loans_count'),
            default => $query->latest(),
        };

        $books = $query->paginate($request->input('per_page', 12));

        return response()->json([
            'data' => $books->map(fn($book) => $this->formatBook($book)),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $book = Book::withoutGlobalScopes()
            ->with(['authors', 'subjects', 'publisher', 'mediaType', 'collectionType', 'contentType', 'carrierType', 'place', 'branch'])
            ->withCount(['items', 'items as available_count' => fn($q) => $q->where('status', 'available')])
            ->findOrFail($id);

        $items = $book->items()
            ->with('location')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'barcode' => $item->barcode,
                'call_number' => $item->call_number ?? $book->call_number,
                'location' => $item->location?->name,
                'status' => $item->status,
                'status_label' => match($item->status) {
                    'available' => 'Tersedia',
                    'on_loan' => 'Dipinjam',
                    'reserved' => 'Dipesan',
                    'damaged' => 'Rusak',
                    'lost' => 'Hilang',
                    default => $item->status,
                },
            ]);

        return response()->json([
            'data' => [
                ...$this->formatBook($book, true),
                'items' => $items,
            ],
        ]);
    }

    public function filters()
    {
        return response()->json([
            'authors' => Author::orderBy('name')->get(['id', 'name']),
            'subjects' => Subject::orderBy('name')->get(['id', 'name']),
            'publishers' => Publisher::orderBy('name')->get(['id', 'name']),
            'gmd' => MediaType::orderBy('name')->get(['id', 'name']),
            'collections' => CollectionType::orderBy('name')->get(['id', 'name']),
            'years' => Book::withoutGlobalScopes()
                ->whereNotNull('publish_year')
                ->distinct()
                ->orderByDesc('publish_year')
                ->pluck('publish_year'),
        ]);
    }

    protected function formatBook($book, $detail = false)
    {
        $data = [
            'id' => $book->id,
            'title' => $book->title,
            'authors' => $book->authors->pluck('name')->join(', ') ?: '-',
            'publisher' => $book->publisher?->name,
            'publish_year' => $book->publish_year,
            'isbn' => $book->isbn,
            'call_number' => $book->call_number,
            'cover' => $book->cover ? asset('storage/' . $book->cover) : null,
            'gmd' => $book->mediaType?->name,
            'collection' => $book->collectionType?->name,
            'items_count' => $book->items_count ?? 0,
            'available_count' => $book->available_count ?? 0,
        ];

        if ($detail) {
            $data += [
                'edition' => $book->edition,
                'collation' => $book->collation,
                'series_title' => $book->series_title,
                'classification' => $book->classification,
                'language' => $book->language,
                'abstract' => $book->abstract,
                'notes' => $book->notes,
                'subjects' => $book->subjects->pluck('name'),
                'content_type' => $book->contentType?->name,
                'carrier_type' => $book->carrierType?->name,
                'publish_place' => $book->place?->name,
                'branch' => $book->branch?->name,
            ];
        }

        return $data;
    }
}
