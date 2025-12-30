<?php

namespace App\Livewire\Opac\Plagiarism;

use App\Models\PlagiarismCheck;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExternalPlagiarismSubmit extends Component
{
    use WithFileUploads;

    public string $document_title = '';
    public string $platform = '';
    public $document_file;
    public $report_file;
    public ?float $similarity_score = null;

    protected $rules = [
        'document_title' => 'required|string|max:500',
        'platform' => 'required|in:turnitin,ithenticate,copyscape',
        'document_file' => 'required|file|mimes:pdf|max:51200',
        'report_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        'similarity_score' => 'required|numeric|min:0|max:100',
    ];

    protected $messages = [
        'document_title.required' => 'Judul dokumen wajib diisi',
        'platform.required' => 'Pilih platform yang digunakan',
        'document_file.required' => 'File dokumen wajib diunggah',
        'document_file.max' => 'Ukuran dokumen maksimal 50MB',
        'report_file.required' => 'File bukti report wajib diunggah',
        'report_file.max' => 'Ukuran report maksimal 10MB',
        'similarity_score.required' => 'Skor similarity wajib diisi',
    ];

    public function submit()
    {
        $this->validate();

        $member = auth('member')->user();

        // Store files
        $documentPath = $this->document_file->store('plagiarism/documents', 'public');
        $reportPath = $this->report_file->store('plagiarism/external-reports', 'public');

        // Create check record
        $check = PlagiarismCheck::create([
            'member_id' => $member->id,
            'document_title' => $this->document_title,
            'original_filename' => $this->document_file->getClientOriginalName(),
            'file_path' => $documentPath,
            'file_type' => $this->document_file->getMimeType(),
            'file_size' => $this->document_file->getSize(),
            'status' => 'pending',
            'check_type' => 'external',
            'external_platform' => $this->platform,
            'external_report_file' => $reportPath,
            'similarity_score' => $this->similarity_score,
            'provider' => $this->platform,
        ]);

        session()->flash('success', 'Pengajuan berhasil dikirim! Silakan tunggu review dari pustakawan.');
        
        return redirect()->route('opac.plagiarism.show', $check);
    }

    public function render()
    {
        return view('livewire.opac.plagiarism.external-plagiarism-submit');
    }
}
