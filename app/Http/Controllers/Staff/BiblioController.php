<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Item;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Subject;
use Illuminate\Http\Request;

class BiblioController extends Controller
{
    protected function getBranchId()
    {
        $user = auth()->user();
        return $user->branch_id ?? session('staff_branch_id') ?? 1;
    }

    public function index(Request $request)
    {
        $branchId = $this->getBranchId();
        
        $query = Book::with(['publisher', 'mediaType', 'authors'])
            ->withCount('items')
            ->where('branch_id', $branchId);

        if ($search = $request->get('search')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('call_number', 'like', "%{$search}%")
                  ->orWhereHas('authors', fn($a) => $a->where('name', 'like', "%{$search}%"));
            });
        }

        $books = $query->latest()->paginate(20)->withQueryString();

        return view('staff.biblio.index', compact('books'));
    }

    public function create()
    {
        return view('staff.biblio.form', [
            'book' => null,
            'publishers' => Publisher::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:500',
            'isbn' => 'nullable|max:20',
            'publisher_id' => 'nullable|exists:publishers,id',
            'publish_year' => 'nullable|max:4',
            'call_number' => 'nullable|max:50',
            'classification' => 'nullable|max:40',
            'item_qty' => 'nullable|integer|min:0|max:100',
        ]);

        $validated['branch_id'] = $this->getBranchId();
        $validated['user_id'] = auth()->id();

        $book = Book::create($validated);

        // Sync authors & subjects
        if ($request->authors) {
            $book->authors()->sync($request->authors);
        }
        if ($request->subjects) {
            $book->subjects()->sync($request->subjects);
        }

        // Create items
        $qty = $request->item_qty ?? 0;
        if ($qty > 0) {
            $this->createItems($book, $qty);
        }

        return redirect()->route('staff.biblio.index')->with('success', 'Bibliografi berhasil ditambahkan');
    }

    public function show(Book $book)
    {
        $book->load(['publisher', 'mediaType', 'authors', 'subjects', 'items.itemStatus', 'items.location']);
        return view('staff.biblio.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $book->load(['authors', 'subjects']);
        return view('staff.biblio.form', [
            'book' => $book,
            'publishers' => Publisher::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|max:500',
            'isbn' => 'nullable|max:20',
            'publisher_id' => 'nullable|exists:publishers,id',
            'publish_year' => 'nullable|max:4',
            'call_number' => 'nullable|max:50',
            'classification' => 'nullable|max:40',
        ]);

        $book->update($validated);

        if ($request->has('authors')) {
            $book->authors()->sync($request->authors ?? []);
        }
        if ($request->has('subjects')) {
            $book->subjects()->sync($request->subjects ?? []);
        }

        return redirect()->route('staff.biblio.show', $book)->with('success', 'Bibliografi berhasil diupdate');
    }

    public function addItems(Request $request, Book $book)
    {
        $request->validate(['qty' => 'required|integer|min:1|max:50']);
        $this->createItems($book, $request->qty);
        return back()->with('success', "{$request->qty} eksemplar berhasil ditambahkan");
    }

    protected function createItems(Book $book, int $qty): void
    {
        $lastItem = Item::orderBy('id', 'desc')->first();
        $lastNumber = $lastItem ? intval(substr($lastItem->barcode, -6)) : 0;

        for ($i = 0; $i < $qty; $i++) {
            $lastNumber++;
            Item::create([
                'book_id' => $book->id,
                'branch_id' => $book->branch_id,
                'barcode' => 'B' . str_pad($lastNumber, 6, '0', STR_PAD_LEFT),
                'call_number' => $book->call_number,
                'collection_type_id' => 1,
                'location_id' => 1,
                'item_status_id' => 1,
                'user_id' => auth()->id(),
            ]);
        }
    }
}
