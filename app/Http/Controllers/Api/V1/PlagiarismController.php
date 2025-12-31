<?php

namespace App\Http\Controllers\Api\V1;

use App\Jobs\ProcessPlagiarismCheck;
use App\Models\PlagiarismCheck;
use App\Services\Plagiarism\CertificateGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlagiarismController extends BaseController
{
    public function index(Request $request)
    {
        $checks = $request->user()->plagiarismChecks()
            ->orderByDesc('created_at')
            ->get();

        return $this->success($checks->map(fn($c) => $this->formatCheck($c)));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_title' => 'required|string|max:500',
            'document' => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $file = $request->file('document');
        $path = $file->store('plagiarism/documents', 'public');

        $check = PlagiarismCheck::create([
            'member_id' => $request->user()->id,
            'document_title' => $request->document_title,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
            'check_type' => 'system',
        ]);

        ProcessPlagiarismCheck::dispatch($check);

        return $this->success([
            'id' => $check->id,
            'status' => 'pending',
            'status_label' => 'Menunggu Antrian',
            'estimated_time' => '5-15 menit',
        ], 'Dokumen berhasil diupload', 201);
    }

    public function show(Request $request, $id)
    {
        $check = $request->user()->plagiarismChecks()->find($id);

        if (!$check) {
            return $this->error('Data tidak ditemukan', 404);
        }

        return $this->success($this->formatCheckDetail($check));
    }

    public function certificate(Request $request, $id)
    {
        $check = $request->user()->plagiarismChecks()->find($id);

        if (!$check || !$check->hasCertificate()) {
            return $this->error('Sertifikat tidak tersedia', 404);
        }

        $generator = new CertificateGenerator($check);

        return response($generator->getPdfContent(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $generator->getDownloadFilename() . '"',
        ]);
    }

    public function storeExternal(Request $request)
    {
        $request->validate([
            'document_title' => 'required|string|max:500',
            'document' => 'required|file|mimes:pdf,doc,docx|max:20480',
            'report' => 'required|file|mimes:pdf|max:10240',
            'platform' => 'required|in:turnitin,ithenticate,copyscape',
            'similarity_score' => 'required|numeric|min:0|max:100',
        ]);

        $docPath = $request->file('document')->store('plagiarism/documents', 'public');
        $reportPath = $request->file('report')->store('plagiarism/external-reports', 'public');

        $check = PlagiarismCheck::create([
            'member_id' => $request->user()->id,
            'document_title' => $request->document_title,
            'original_filename' => $request->file('document')->getClientOriginalName(),
            'file_path' => $docPath,
            'file_type' => $request->file('document')->getClientOriginalExtension(),
            'file_size' => $request->file('document')->getSize(),
            'status' => 'pending',
            'check_type' => 'external',
            'external_platform' => $request->platform,
            'external_report_file' => $reportPath,
            'similarity_score' => $request->similarity_score,
        ]);

        return $this->success([
            'id' => $check->id,
            'status' => 'pending',
            'status_label' => 'Menunggu Review',
            'message' => 'Pengajuan akan direview oleh pustakawan',
        ], 'Pengajuan berhasil dikirim', 201);
    }

    protected function formatCheck(PlagiarismCheck $c): array
    {
        return [
            'id' => $c->id,
            'document_title' => $c->document_title,
            'original_filename' => $c->original_filename,
            'file_size' => $c->file_size_formatted,
            'status' => $c->status,
            'status_label' => $c->status_label,
            'similarity_score' => $c->similarity_score,
            'similarity_level' => $c->similarity_level,
            'similarity_label' => $c->similarity_label,
            'is_passed' => $c->isPassed(),
            'provider' => $c->provider_label,
            'certificate_number' => $c->certificate_number,
            'has_certificate' => $c->hasCertificate(),
            'created_at' => $c->created_at?->toIso8601String(),
            'completed_at' => $c->completed_at?->toIso8601String(),
        ];
    }

    protected function formatCheckDetail(PlagiarismCheck $c): array
    {
        $data = $this->formatCheck($c);
        $data['word_count'] = $c->word_count;
        $data['page_count'] = $c->page_count;
        $data['status_info'] = $c->status_info;
        $data['pass_threshold'] = (float) \App\Models\Setting::get('plagiarism_pass_threshold', 25);
        $data['processing_time'] = $c->processing_time;
        $data['started_at'] = $c->started_at?->toIso8601String();
        $data['certificate_url'] = $c->hasCertificate() ? "/api/v1/plagiarism/{$c->id}/certificate" : null;
        return $data;
    }
}
