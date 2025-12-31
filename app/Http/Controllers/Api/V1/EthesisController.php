<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ethesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EthesisController extends BaseController
{
    public function index(Request $request)
    {
        $query = Ethesis::with(['department.faculty'])->where('is_public', true);

        if ($q = $request->q) {
            $query->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('nim', 'like', "%{$q}%");
            });
        }

        if ($request->faculty_id) {
            $query->whereHas('department', fn($q) => $q->where('faculty_id', $request->faculty_id));
        }
        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->year) {
            $query->where('year', $request->year);
        }

        $etheses = $query->orderByDesc('year')->orderByDesc('created_at')->paginate($request->per_page ?? 20);

        return $this->paginated($etheses->through(fn($e) => $this->formatEthesis($e)));
    }

    public function show($id)
    {
        $ethesis = Ethesis::with(['department.faculty'])->find($id);

        if (!$ethesis || !$ethesis->is_public) {
            return $this->error('E-Thesis tidak ditemukan', 404);
        }

        return $this->success($this->formatEthesisDetail($ethesis));
    }

    protected function formatEthesis(Ethesis $e): array
    {
        return [
            'id' => $e->id,
            'title' => $e->title,
            'author' => $e->author,
            'nim' => $e->nim,
            'department' => $e->department?->name,
            'faculty' => $e->department?->faculty?->name,
            'year' => $e->year,
            'type' => $e->type,
            'cover_url' => $e->cover_path ? Storage::disk('public')->url($e->cover_path) : null,
            'is_fulltext_public' => $e->is_fulltext_public,
        ];
    }

    protected function formatEthesisDetail(Ethesis $e): array
    {
        return [
            'id' => $e->id,
            'title' => $e->title,
            'title_en' => $e->title_en,
            'author' => $e->author,
            'nim' => $e->nim,
            'advisor1' => $e->advisor1,
            'advisor2' => $e->advisor2,
            'department' => $e->department ? ['id' => $e->department->id, 'name' => $e->department->name] : null,
            'faculty' => $e->department?->faculty ? ['id' => $e->department->faculty->id, 'name' => $e->department->faculty->name] : null,
            'year' => $e->year,
            'defense_date' => $e->defense_date?->format('Y-m-d'),
            'type' => $e->type,
            'abstract' => $e->abstract,
            'abstract_en' => $e->abstract_en,
            'keywords' => $e->keywords ? explode(',', $e->keywords) : [],
            'cover_url' => $e->cover_path ? Storage::disk('public')->url($e->cover_path) : null,
            'is_fulltext_public' => $e->is_fulltext_public,
            'download_url' => $e->is_fulltext_public && $e->file_path ? route('api.v1.ethesis.download', $e->id) : null,
        ];
    }
}
