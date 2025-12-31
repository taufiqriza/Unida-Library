<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Book;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\Faculty;
use App\Models\News;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneralController extends BaseController
{
    public function index()
    {
        return $this->success([
            'stats' => [
                'books' => Book::count(),
                'ebooks' => Ebook::where('is_active', true)->where('opac_hide', false)->count(),
                'etheses' => Ethesis::where('is_public', true)->count(),
                'branches' => Branch::where('is_active', true)->count(),
            ],
            'new_books' => Book::with('authors')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn($b) => [
                    'id' => $b->id,
                    'title' => $b->title,
                    'authors' => $b->authors->pluck('name'),
                    'cover_url' => $b->cover ? Storage::disk('public')->url($b->cover) : null,
                ]),
            'news' => News::where('status', 'published')
                ->orderByDesc('published_at')
                ->limit(3)
                ->get()
                ->map(fn($n) => [
                    'id' => $n->id,
                    'title' => $n->title,
                    'slug' => $n->slug,
                    'excerpt' => $n->excerpt ?: \Str::limit(strip_tags($n->content), 100),
                    'image_url' => $n->featured_image ? Storage::disk('public')->url($n->featured_image) : null,
                    'published_at' => $n->published_at?->toIso8601String(),
                ]),
        ]);
    }

    public function branches()
    {
        $branches = Branch::where('is_active', true)
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'address', 'phone', 'email', 'is_main']);

        return $this->success($branches);
    }

    public function faculties()
    {
        $faculties = Faculty::orderBy('name')->get(['id', 'name']);
        return $this->success($faculties);
    }

    public function departments(Request $request)
    {
        $query = Department::orderBy('name');

        if ($request->faculty_id) {
            $query->where('faculty_id', $request->faculty_id);
        }

        return $this->success($query->get(['id', 'name', 'faculty_id']));
    }

    public function settings()
    {
        return $this->success([
            'app_name' => Setting::get('app_name', 'Perpustakaan UNIDA Gontor'),
            'app_logo' => Setting::get('app_logo') ? Storage::disk('public')->url(Setting::get('app_logo')) : null,
            'contact_email' => Setting::get('contact_email', 'perpus@unida.gontor.ac.id'),
            'contact_phone' => Setting::get('contact_phone'),
            'contact_whatsapp' => Setting::get('contact_whatsapp'),
            'address' => Setting::get('address', 'Kampus UNIDA Gontor, Ponorogo'),
            'operating_hours' => Setting::get('operating_hours', 'Senin-Jumat: 08:00-16:00'),
            'fine_per_day' => (int) Setting::get('fine_per_day', 1000),
            'loan_period_days' => (int) Setting::get('loan_period_days', 14),
            'max_renew' => (int) Setting::get('max_renew', 2),
        ]);
    }

    public function news(Request $request)
    {
        $news = News::where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate($request->per_page ?? 10);

        return $this->paginated($news->through(fn($n) => [
            'id' => $n->id,
            'title' => $n->title,
            'slug' => $n->slug,
            'excerpt' => $n->excerpt ?: \Str::limit(strip_tags($n->content), 150),
            'image_url' => $n->featured_image ? Storage::disk('public')->url($n->featured_image) : null,
            'category' => $n->category?->name,
            'published_at' => $n->published_at?->toIso8601String(),
        ]));
    }

    public function newsShow($slug)
    {
        $news = News::where('slug', $slug)->where('status', 'published')->first();

        if (!$news) {
            return $this->error('Berita tidak ditemukan', 404);
        }

        return $this->success([
            'id' => $news->id,
            'title' => $news->title,
            'slug' => $news->slug,
            'content' => $news->content,
            'image_url' => $news->featured_image ? Storage::disk('public')->url($news->featured_image) : null,
            'category' => $news->category?->name,
            'author' => $news->author?->name,
            'published_at' => $news->published_at?->toIso8601String(),
        ]);
    }
}
