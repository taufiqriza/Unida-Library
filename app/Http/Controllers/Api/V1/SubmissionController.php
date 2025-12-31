<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ThesisSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends BaseController
{
    public function index(Request $request)
    {
        $submissions = $request->user()->thesisSubmissions()
            ->with(['department.faculty', 'clearanceLetter'])
            ->orderByDesc('created_at')
            ->get();

        return $this->success($submissions->map(fn($s) => $this->formatSubmission($s)));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'abstract' => 'required|string',
            'abstract_en' => 'nullable|string',
            'type' => 'required|in:skripsi,tesis,disertasi',
            'department_id' => 'required|exists:departments,id',
            'year' => 'required|integer|min:2000|max:2100',
            'defense_date' => 'nullable|date',
            'advisor1' => 'required|string|max:255',
            'advisor2' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:500',
            'cover_file' => 'required|image|max:2048',
            'fulltext_file' => 'required|file|mimes:pdf|max:51200',
            'fulltext_visible' => 'nullable|boolean',
        ]);

        $member = $request->user();

        $coverPath = $request->file('cover_file')->store('thesis/covers', 'public');
        $fulltextPath = $request->file('fulltext_file')->store('thesis/fulltext', 'public');

        $submission = ThesisSubmission::create([
            'member_id' => $member->id,
            'department_id' => $request->department_id,
            'title' => $request->title,
            'title_en' => $request->title_en,
            'abstract' => $request->abstract,
            'abstract_en' => $request->abstract_en,
            'author' => $member->name,
            'nim' => $member->member_id,
            'type' => $request->type,
            'year' => $request->year,
            'defense_date' => $request->defense_date,
            'advisor1' => $request->advisor1,
            'advisor2' => $request->advisor2,
            'keywords' => $request->keywords,
            'cover_file' => $coverPath,
            'fulltext_file' => $fulltextPath,
            'fulltext_visible' => $request->boolean('fulltext_visible', true),
            'status' => 'submitted',
        ]);

        return $this->success($this->formatSubmission($submission), 'Pengajuan berhasil dikirim', 201);
    }

    public function show(Request $request, $id)
    {
        $submission = $request->user()->thesisSubmissions()
            ->with(['department.faculty', 'clearanceLetter', 'reviewer'])
            ->find($id);

        if (!$submission) {
            return $this->error('Data tidak ditemukan', 404);
        }

        return $this->success($this->formatSubmissionDetail($submission));
    }

    public function update(Request $request, $id)
    {
        $submission = $request->user()->thesisSubmissions()->find($id);

        if (!$submission) {
            return $this->error('Data tidak ditemukan', 404);
        }

        if (!in_array($submission->status, ['submitted', 'revision_required'])) {
            return $this->error('Pengajuan tidak dapat diubah', 400);
        }

        $request->validate([
            'title' => 'sometimes|string|max:500',
            'abstract' => 'sometimes|string',
            'cover_file' => 'sometimes|image|max:2048',
            'fulltext_file' => 'sometimes|file|mimes:pdf|max:51200',
        ]);

        if ($request->hasFile('cover_file')) {
            Storage::disk('public')->delete($submission->cover_file);
            $submission->cover_file = $request->file('cover_file')->store('thesis/covers', 'public');
        }

        if ($request->hasFile('fulltext_file')) {
            Storage::disk('public')->delete($submission->fulltext_file);
            $submission->fulltext_file = $request->file('fulltext_file')->store('thesis/fulltext', 'public');
        }

        $submission->fill($request->only(['title', 'title_en', 'abstract', 'abstract_en', 'keywords']));
        $submission->status = 'submitted';
        $submission->save();

        return $this->success($this->formatSubmission($submission), 'Pengajuan berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $submission = $request->user()->thesisSubmissions()->find($id);

        if (!$submission) {
            return $this->error('Data tidak ditemukan', 404);
        }

        if (!in_array($submission->status, ['submitted', 'rejected'])) {
            return $this->error('Pengajuan tidak dapat dibatalkan', 400);
        }

        Storage::disk('public')->delete([$submission->cover_file, $submission->fulltext_file]);
        $submission->delete();

        return $this->success(null, 'Pengajuan berhasil dibatalkan');
    }

    protected function formatSubmission(ThesisSubmission $s): array
    {
        return [
            'id' => $s->id,
            'title' => $s->title,
            'type' => $s->type,
            'type_label' => ucfirst($s->type),
            'department' => $s->department?->name,
            'status' => $s->status,
            'status_label' => $s->status_label,
            'has_clearance_letter' => $s->clearanceLetter !== null,
            'created_at' => $s->created_at?->toIso8601String(),
            'reviewed_at' => $s->reviewed_at?->toIso8601String(),
        ];
    }

    protected function formatSubmissionDetail(ThesisSubmission $s): array
    {
        return [
            'id' => $s->id,
            'title' => $s->title,
            'title_en' => $s->title_en,
            'abstract' => $s->abstract,
            'abstract_en' => $s->abstract_en,
            'author' => $s->author,
            'nim' => $s->nim,
            'type' => $s->type,
            'department' => $s->department ? ['id' => $s->department->id, 'name' => $s->department->name] : null,
            'faculty' => $s->department?->faculty ? ['id' => $s->department->faculty->id, 'name' => $s->department->faculty->name] : null,
            'year' => $s->year,
            'defense_date' => $s->defense_date?->format('Y-m-d'),
            'advisor1' => $s->advisor1,
            'advisor2' => $s->advisor2,
            'keywords' => $s->keywords ? explode(',', $s->keywords) : [],
            'status' => $s->status,
            'status_label' => $s->status_label,
            'review_notes' => $s->review_notes,
            'rejection_reason' => $s->rejection_reason,
            'reviewed_by' => $s->reviewer?->name,
            'reviewed_at' => $s->reviewed_at?->toIso8601String(),
            'cover_url' => $s->cover_file ? Storage::disk('public')->url($s->cover_file) : null,
            'fulltext_visible' => $s->fulltext_visible,
            'clearance_letter' => $s->clearanceLetter ? [
                'id' => $s->clearanceLetter->id,
                'letter_number' => $s->clearanceLetter->letter_number,
                'status' => $s->clearanceLetter->status,
            ] : null,
            'created_at' => $s->created_at?->toIso8601String(),
        ];
    }
}
