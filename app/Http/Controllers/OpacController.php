<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Branch;
use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\Item;
use App\Models\Member;
use App\Models\News;
use App\Models\Subject;
use Illuminate\Http\Request;

class OpacController extends Controller
{
    public function home()
    {
        $stats = [
            'books' => Book::withoutGlobalScopes()->count(),
            'items' => Item::withoutGlobalScopes()->count(),
            'members' => Member::withoutGlobalScopes()->count(),
            'ebooks' => Ebook::count(),
            'etheses' => Ethesis::count(),
        ];

        $newBooks = Book::withoutGlobalScopes()
            ->with('authors')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->title,
                'authors' => $b->author_names ?: '-',
                'cover' => $b->cover_url,
                'publish_year' => $b->publish_year,
            ]);

        $popularBooks = Book::withoutGlobalScopes()
            ->with('authors')
            ->withCount(['items as loans_count' => fn($q) => $q->withoutGlobalScopes()->whereHas('loans')])
            ->orderByDesc('loans_count')
            ->take(8)
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->title,
                'authors' => $b->author_names ?: '-',
                'cover' => $b->cover_url,
                'loans_count' => $b->loans_count,
            ]);

        $news = News::published()
            ->latest('published_at')
            ->take(4)
            ->get()
            ->map(fn($n) => [
                'slug' => $n->slug,
                'title' => $n->title,
                'excerpt' => $n->excerpt,
                'image' => $n->image_url,
                'published_at' => $n->published_at?->format('d M Y'),
            ]);

        $branches = Branch::all()->map(fn($b) => [
            'name' => $b->name,
            'address' => $b->address ?? '',
        ]);

        return view('opac.home', compact('stats', 'newBooks', 'popularBooks', 'news', 'branches'));
    }

    public function catalog(Request $request)
    {
        $query = Book::withoutGlobalScopes()->with('authors')->withCount('items');

        if ($q = $request->q) {
            $query->where(fn($qb) => $qb
                ->where('title', 'like', "%{$q}%")
                ->orWhereHas('authors', fn($a) => $a->where('name', 'like', "%{$q}%"))
                ->orWhere('isbn', 'like', "%{$q}%"));
        }

        if ($subject = $request->subject) {
            $query->whereHas('subjects', fn($s) => $s->where('subjects.id', $subject));
        }

        if ($branch = $request->branch) {
            $query->whereHas('items', fn($i) => $i->withoutGlobalScopes()->where('branch_id', $branch));
        }

        $sort = $request->sort ?? 'latest';
        match ($sort) {
            'title' => $query->orderBy('title'),
            'popular' => $query->withCount(['items as loans_count' => fn($q) => $q->withoutGlobalScopes()->whereHas('loans')])->orderByDesc('loans_count'),
            default => $query->latest(),
        };

        $books = $query->paginate(12)->withQueryString();
        $subjects = Subject::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('opac.catalog', compact('books', 'subjects', 'branches'));
    }

    public function catalogShow($id)
    {
        $book = Book::withoutGlobalScopes()
            ->with(['publisher', 'items' => fn($q) => $q->withoutGlobalScopes()->with('branch'), 'authors', 'subjects'])
            ->findOrFail($id);

        $relatedBooks = collect();
        if ($book->subjects->isNotEmpty()) {
            $relatedBooks = Book::withoutGlobalScopes()
                ->with('authors')
                ->whereHas('subjects', fn($q) => $q->whereIn('subjects.id', $book->subjects->pluck('id')))
                ->where('id', '!=', $book->id)
                ->take(4)
                ->get();
        }

        return view('opac.catalog-detail', compact('book', 'relatedBooks'));
    }

    public function ebooks(Request $request)
    {
        $query = Ebook::with('authors')->where('is_active', true);

        if ($q = $request->q) {
            $query->where(fn($qb) => $qb
                ->where('title', 'like', "%{$q}%")
                ->orWhereHas('authors', fn($a) => $a->where('name', 'like', "%{$q}%")));
        }

        $books = $query->latest()->paginate(12)->withQueryString();

        return view('opac.ebooks', compact('books'));
    }

    public function etheses(Request $request)
    {
        $query = Ethesis::where('is_public', true);

        if ($q = $request->q) {
            $query->where(fn($qb) => $qb
                ->where('title', 'like', "%{$q}%")
                ->orWhere('author', 'like', "%{$q}%"));
        }

        $theses = $query->latest()->paginate(12)->withQueryString();

        return view('opac.etheses', compact('theses'));
    }

    public function news()
    {
        $news = News::published()->latest('published_at')->paginate(12);
        return view('opac.news', compact('news'));
    }

    public function newsShow($slug)
    {
        $news = News::where('slug', $slug)->published()->firstOrFail();
        $recentNews = News::published()
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('opac.news-detail', compact('news', 'recentNews'));
    }

    public function page($slug)
    {
        // Pages with dedicated views
        $dedicatedViews = [
            // HOME
            'visi-misi', 'sejarah', 'struktur-organisasi', 'mou',
            'tata-tertib', 'jam-layanan', 'fasilitas', 'karir',
            // E-RESOURCES
            'digilib-apps', 'journal-subscription',
            // DISCOVER
            'event-library', 'virtual-tour', 'e-learning',
            'prosa-kreatif', 'survey', 'research-tools',
            // GUIDE
            'panduan-opac', 'unggah-tugas-akhir', 'panduan-ospek',
            'panduan-akademik', 'materi-perpustakaan', 'download-eddc',
        ];

        if (in_array($slug, $dedicatedViews)) {
            return view('opac.pages.' . $slug);
        }

        // Generic pages
        $pages = [
            // E-RESOURCES
            'digilib-apps' => [
                'title' => 'Digilib Apps',
                'subtitle' => 'Aplikasi Perpustakaan Digital',
                'icon' => 'fas fa-mobile-alt',
                'content' => null,
            ],
            'journal-subscription' => [
                'title' => 'Journal Subscription',
                'subtitle' => 'Langganan Jurnal Internasional',
                'icon' => 'fas fa-bookmark',
                'content' => null,
            ],
            // DISCOVER
            'event-library' => [
                'title' => 'Event Library',
                'subtitle' => 'Kegiatan dan Acara Perpustakaan',
                'icon' => 'fas fa-calendar-star',
                'content' => null,
            ],
            'virtual-tour' => [
                'title' => 'Virtual Tour',
                'subtitle' => 'Tur Virtual 360° Perpustakaan',
                'icon' => 'fas fa-vr-cardboard',
                'content' => null,
            ],
            'e-learning' => [
                'title' => 'E-Learning',
                'subtitle' => 'Pembelajaran Online',
                'icon' => 'fas fa-laptop',
                'content' => null,
            ],
            'prosa-kreatif' => [
                'title' => 'Prosa Kreatif',
                'subtitle' => 'Komunitas Menulis Kreatif',
                'icon' => 'fas fa-pen-fancy',
                'content' => null,
            ],
            'survey' => [
                'title' => 'Experience Survey',
                'subtitle' => 'Survei Kepuasan Pengguna',
                'icon' => 'fas fa-poll',
                'content' => null,
            ],
            'research-tools' => [
                'title' => 'Research Tools',
                'subtitle' => 'Alat Bantu Penelitian',
                'icon' => 'fas fa-microscope',
                'content' => null,
            ],
            // GUIDE
            'panduan-opac' => [
                'title' => 'Panduan OPAC',
                'subtitle' => 'Cara Menggunakan Katalog Online',
                'icon' => 'fas fa-search',
                'content' => null,
            ],
            'unggah-tugas-akhir' => [
                'title' => 'Unggah Tugas Akhir',
                'subtitle' => 'Panduan Upload Skripsi/Tesis',
                'icon' => 'fas fa-upload',
                'content' => null,
            ],
            'panduan-ospek' => [
                'title' => 'Panduan Ospek',
                'subtitle' => 'Orientasi Perpustakaan Mahasiswa Baru',
                'icon' => 'fas fa-user-graduate',
                'content' => null,
            ],
            'panduan-akademik' => [
                'title' => 'Panduan Akademik',
                'subtitle' => 'Informasi Akademik Perpustakaan',
                'icon' => 'fas fa-book-reader',
                'content' => null,
            ],
            'materi-perpustakaan' => [
                'title' => 'Materi Perpustakaan',
                'subtitle' => 'Bahan Ajar dan Presentasi',
                'icon' => 'fas fa-chalkboard-teacher',
                'content' => null,
            ],
            'download-eddc' => [
                'title' => 'Download E-DDC 23',
                'subtitle' => 'Dewey Decimal Classification',
                'icon' => 'fas fa-download',
                'content' => null,
            ],
        ];

        if (!isset($pages[$slug])) {
            abort(404);
        }

        $page = $pages[$slug];

        return view('opac.page', compact('page'));
    }
}
