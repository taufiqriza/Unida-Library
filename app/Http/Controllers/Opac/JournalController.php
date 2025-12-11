<?php

namespace App\Http\Controllers\Opac;

use App\Http\Controllers\Controller;
use App\Models\JournalArticle;
use App\Models\JournalSource;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $sources = JournalSource::where('is_active', true)
            ->withCount('articles')
            ->orderBy('name')
            ->get();

        $query = JournalArticle::query()->with('source');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%");
            });
        }

        if ($request->filled('journal')) {
            $query->where('journal_code', $request->journal);
        }

        if ($request->filled('year')) {
            $query->where('publish_year', $request->year);
        }

        $articles = $query->orderByDesc('published_at')->paginate(20);

        $years = JournalArticle::selectRaw('DISTINCT publish_year')
            ->whereNotNull('publish_year')
            ->orderByDesc('publish_year')
            ->pluck('publish_year');

        return view('opac.journals.index', compact('sources', 'articles', 'years'));
    }

    public function show(JournalArticle $article)
    {
        $article->load('source');
        $article->incrementViews();

        // Related articles from same journal/source
        $related = JournalArticle::where('id', '!=', $article->id)
            ->when($article->source_type === 'repo', 
                fn($q) => $q->where('source_type', 'repo'),
                fn($q) => $q->where('journal_code', $article->journal_code)
            )
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        // Use different view for repo articles
        $view = $article->source_type === 'repo' ? 'opac.journals.show-repo' : 'opac.journals.show';
        
        return view($view, compact('article', 'related'));
    }
}
