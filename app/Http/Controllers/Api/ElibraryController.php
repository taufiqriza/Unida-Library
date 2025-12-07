<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\News;
use Illuminate\Http\Request;

class ElibraryController extends Controller
{
    public function ebooks(Request $request)
    {
        $query = Ebook::with(['authors', 'subjects'])->where('is_active', true);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('abstract', 'like', "%{$search}%")
                    ->orWhereHas('authors', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $ebooks = $query->latest()->paginate(12);

        return response()->json([
            'data' => $ebooks->map(fn($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'authors' => $e->authors->pluck('name')->join(', ') ?: '-',
                'cover' => $e->cover ? asset('storage/' . $e->cover) : null,
                'publish_year' => $e->publish_year,
                'file_type' => strtoupper(pathinfo($e->file, PATHINFO_EXTENSION)),
                'download_count' => $e->download_count,
            ]),
            'meta' => [
                'current_page' => $ebooks->currentPage(),
                'last_page' => $ebooks->lastPage(),
                'total' => $ebooks->total(),
            ],
        ]);
    }

    public function ebookShow($id)
    {
        $ebook = Ebook::with(['authors', 'subjects'])->where('is_active', true)->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $ebook->id,
                'title' => $ebook->title,
                'authors' => $ebook->authors->pluck('name'),
                'subjects' => $ebook->subjects->pluck('name'),
                'publisher' => $ebook->publisher,
                'publish_year' => $ebook->publish_year,
                'isbn' => $ebook->isbn,
                'abstract' => $ebook->abstract,
                'cover' => $ebook->cover ? asset('storage/' . $ebook->cover) : null,
                'file_type' => strtoupper(pathinfo($ebook->file, PATHINFO_EXTENSION)),
                'download_count' => $ebook->download_count,
            ],
        ]);
    }

    public function etheses(Request $request)
    {
        $query = Ethesis::with(['department.faculty'])->where('is_public', true);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('abstract', 'like', "%{$search}%");
            });
        }

        if ($faculty = $request->input('faculty')) {
            $query->whereHas('department', fn($q) => $q->where('faculty_id', $faculty));
        }
        if ($department = $request->input('department')) {
            $query->where('department_id', $department);
        }
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        if ($year = $request->input('year')) {
            $query->where('year', $year);
        }

        $theses = $query->latest()->paginate(12);

        return response()->json([
            'data' => $theses->map(fn($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'author' => $t->author,
                'type' => $t->type,
                'type_label' => match($t->type) {
                    'skripsi' => 'Skripsi',
                    'tesis' => 'Tesis',
                    'disertasi' => 'Disertasi',
                    default => $t->type,
                },
                'year' => $t->year,
                'department' => $t->department?->name,
                'faculty' => $t->department?->faculty?->name,
                'cover' => $t->cover_path ? asset('storage/' . $t->cover_path) : null,
            ]),
            'meta' => [
                'current_page' => $theses->currentPage(),
                'last_page' => $theses->lastPage(),
                'total' => $theses->total(),
            ],
        ]);
    }

    public function ethesisShow($id)
    {
        $thesis = Ethesis::with(['department.faculty', 'subjects'])->where('is_public', true)->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $thesis->id,
                'title' => $thesis->title,
                'title_en' => $thesis->title_en,
                'author' => $thesis->author,
                'nim' => $thesis->nim,
                'type' => $thesis->type,
                'year' => $thesis->year,
                'abstract' => $thesis->abstract,
                'abstract_en' => $thesis->abstract_en,
                'keywords' => $thesis->keywords,
                'advisor1' => $thesis->advisor1,
                'advisor2' => $thesis->advisor2,
                'examiner1' => $thesis->examiner1,
                'examiner2' => $thesis->examiner2,
                'department' => $thesis->department?->name,
                'faculty' => $thesis->department?->faculty?->name,
                'subjects' => $thesis->subjects?->pluck('name') ?? [],
                'cover' => $thesis->cover_path ? asset('storage/' . $thesis->cover_path) : null,
                'has_fulltext' => !empty($thesis->file_path) && $thesis->is_fulltext_public,
                'views' => $thesis->views,
            ],
        ]);
    }

    public function news(Request $request)
    {
        $news = News::with('category')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'data' => $news->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'slug' => $n->slug,
                'excerpt' => $n->excerpt ?? \Str::limit(strip_tags($n->content), 150),
                'image' => $n->image ? asset('storage/' . $n->image) : null,
                'category' => $n->category?->name,
                'published_at' => $n->published_at->format('d M Y'),
                'is_featured' => $n->is_featured,
            ]),
            'meta' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'total' => $news->total(),
            ],
        ]);
    }

    public function newsShow($slug)
    {
        $news = News::with('category')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $news->id,
                'title' => $news->title,
                'content' => $news->content,
                'image' => $news->image ? asset('storage/' . $news->image) : null,
                'category' => $news->category?->name,
                'published_at' => $news->published_at->format('d M Y'),
            ],
        ]);
    }
}
