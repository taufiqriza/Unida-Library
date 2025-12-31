<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EbookController extends BaseController
{
    public function index(Request $request)
    {
        $query = Ebook::with(['authors', 'publisher', 'subjects'])
            ->where('is_active', true)
            ->where('opac_hide', false);

        if ($q = $request->q) {
            $query->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhereHas('authors', fn($aq) => $aq->where('name', 'like', "%{$q}%"));
            });
        }

        if ($request->subject_id) {
            $query->whereHas('subjects', fn($q) => $q->where('subjects.id', $request->subject_id));
        }
        if ($request->year) {
            $query->where('publish_year', $request->year);
        }

        $ebooks = $query->orderByDesc('created_at')->paginate($request->per_page ?? 20);

        return $this->paginated($ebooks->through(fn($ebook) => $this->formatEbook($ebook)));
    }

    public function show($id)
    {
        $ebook = Ebook::with(['authors', 'publisher', 'subjects'])
            ->where('is_active', true)
            ->where('opac_hide', false)
            ->find($id);

        if (!$ebook) {
            return $this->error('E-Book tidak ditemukan', 404);
        }

        return $this->success($this->formatEbookDetail($ebook));
    }

    protected function formatEbook(Ebook $ebook): array
    {
        return [
            'id' => $ebook->id,
            'title' => $ebook->title,
            'authors' => $ebook->authors->pluck('name'),
            'publisher' => $ebook->publisher?->name,
            'year' => $ebook->publish_year,
            'cover_url' => $ebook->cover_image ? Storage::disk('public')->url($ebook->cover_image) : null,
        ];
    }

    protected function formatEbookDetail(Ebook $ebook): array
    {
        return [
            'id' => $ebook->id,
            'title' => $ebook->title,
            'authors' => $ebook->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
            'publisher' => $ebook->publisher ? ['id' => $ebook->publisher->id, 'name' => $ebook->publisher->name] : null,
            'year' => $ebook->publish_year,
            'isbn' => $ebook->isbn,
            'pages' => $ebook->pages,
            'cover_url' => $ebook->cover_image ? Storage::disk('public')->url($ebook->cover_image) : null,
            'abstract' => $ebook->abstract,
            'subjects' => $ebook->subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name]),
            'read_url' => $ebook->google_drive_id ? "https://drive.google.com/file/d/{$ebook->google_drive_id}/preview" : null,
            'is_downloadable' => $ebook->is_downloadable,
            'download_count' => $ebook->download_count ?? 0,
        ];
    }
}
