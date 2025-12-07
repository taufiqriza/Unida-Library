<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use App\Models\News;
use App\Models\Branch;

class HomeController extends Controller
{
    public function index()
    {
        return response()->json([
            'stats' => [
                'books' => Book::withoutGlobalScopes()->count(),
                'items' => Item::withoutGlobalScopes()->count(),
                'members' => Member::count(),
                'ebooks' => Ebook::where('is_active', true)->count(),
                'etheses' => Ethesis::where('is_public', true)->count(),
            ],
            'new_books' => Book::withoutGlobalScopes()
                ->with('authors')
                ->latest()
                ->take(8)
                ->get()
                ->map(fn($b) => [
                    'id' => $b->id,
                    'title' => $b->title,
                    'authors' => $b->authors->pluck('name')->join(', ') ?: '-',
                    'cover' => $b->cover ? asset('storage/' . $b->cover) : null,
                    'publish_year' => $b->publish_year,
                ]),
            'popular_books' => Book::withoutGlobalScopes()
                ->with('authors')
                ->withCount('loans')
                ->orderByDesc('loans_count')
                ->take(8)
                ->get()
                ->map(fn($b) => [
                    'id' => $b->id,
                    'title' => $b->title,
                    'authors' => $b->authors->pluck('name')->join(', ') ?: '-',
                    'cover' => $b->cover ? asset('storage/' . $b->cover) : null,
                    'loans_count' => $b->loans_count,
                ]),
            'news' => News::with('category')
                ->where('status', 'published')
                ->latest('published_at')
                ->take(4)
                ->get()
                ->map(fn($n) => [
                    'id' => $n->id,
                    'title' => $n->title,
                    'slug' => $n->slug,
                    'excerpt' => $n->excerpt ?? \Str::limit(strip_tags($n->content), 100),
                    'image' => $n->image ? asset('storage/' . $n->image) : null,
                    'published_at' => $n->published_at?->format('d M Y'),
                ]),
            'branches' => Branch::where('is_active', true)->get(['id', 'name', 'address']),
        ]);
    }

    public function branches()
    {
        return response()->json([
            'data' => Branch::where('is_active', true)
                ->withCount([
                    'books',
                    'items',
                    'members',
                ])
                ->get()
                ->map(fn($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'code' => $b->code,
                    'address' => $b->address,
                    'phone' => $b->phone,
                    'email' => $b->email,
                    'is_main' => $b->is_main,
                    'books_count' => $b->books_count,
                    'items_count' => $b->items_count,
                    'members_count' => $b->members_count,
                ]),
        ]);
    }
}
