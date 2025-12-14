<?php

namespace App\Livewire\Opac\Plagiarism;

use App\Jobs\ProcessPlagiarismCheck;
use App\Models\PlagiarismCheck;
use App\Models\Setting;
use App\Services\Plagiarism\PlagiarismService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PlagiarismCreate extends Component
{
    use WithFileUploads;

    protected const QUOTA_LIMIT = 3;

    public $member;
    public $submissions;
    public $usedQuota;
    public $remainingQuota;

    public $document;
    public string $document_title = '';
    public ?int $thesis_submission_id = null;
    public bool $agreement = false;

    public function mount()
    {
        if (!PlagiarismService::isEnabled()) {
            session()->flash('error', 'Layanan cek plagiasi sedang tidak aktif.');
            return redirect()->route('opac.member.dashboard');
        }

        $this->member = Auth::guard('member')->user();
        $this->usedQuota = $this->member->plagiarismChecks()->count();
        $this->remainingQuota = self::QUOTA_LIMIT - $this->usedQuota;

        if ($this->remainingQuota <= 0) {
            session()->flash('error', 'Kuota cek plagiasi Anda sudah habis (' . self::QUOTA_LIMIT . ' kali).');
            return redirect()->route('opac.member.plagiarism.index');
        }

        $this->submissions = $this->member->thesisSubmissions()
            ->whereIn('status', ['approved', 'published'])
            ->latest()
            ->get();
    }

    protected function rules()
    {
        return [
            'document' => 'required|file|mimes:pdf,docx|max:20480',
            'document_title' => 'required|string|max:500',
            'thesis_submission_id' => 'nullable|exists:thesis_submissions,id',
            'agreement' => 'required|accepted',
        ];
    }

    protected $messages = [
        'document.required' => 'Dokumen wajib diunggah',
        'document.mimes' => 'Format dokumen harus PDF atau DOCX',
        'document.max' => 'Ukuran dokumen maksimal 20MB',
        'document_title.required' => 'Judul dokumen wajib diisi',
        'agreement.accepted' => 'Anda harus menyetujui pernyataan',
    ];

    public function submit()
    {
        if (!PlagiarismService::isEnabled()) {
            $this->dispatch('notify', type: 'error', message: 'Layanan cek plagiasi sedang tidak aktif.');
            return;
        }

        $this->validate();

        // Check quota
        $usedQuota = $this->member->plagiarismChecks()->count();
        if ($usedQuota >= self::QUOTA_LIMIT) {
            $this->dispatch('notify', type: 'error', message: 'Kuota cek plagiasi Anda sudah habis.');
            return;
        }

        // Store file
        $path = $this->document->store('plagiarism-uploads', 'local');

        // Create check record
        $check = PlagiarismCheck::create([
            'member_id' => $this->member->id,
            'thesis_submission_id' => $this->thesis_submission_id,
            'document_title' => $this->document_title,
            'original_filename' => $this->document->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $this->document->getClientOriginalExtension(),
            'file_size' => $this->document->getSize(),
            'status' => PlagiarismCheck::STATUS_PENDING,
            'provider' => Setting::get('plagiarism_provider', 'ithenticate'),
        ]);

        // Dispatch background job
        ProcessPlagiarismCheck::dispatch($check);

        return redirect()->route('opac.member.plagiarism.show', $check)
            ->with('success', 'Dokumen berhasil diunggah. Pengecekan plagiasi sedang diproses.');
    }

    public function render()
    {
        return view('livewire.opac.plagiarism.plagiarism-create', [
            'quotaLimit' => self::QUOTA_LIMIT,
        ])->layout('components.opac.layout', ['title' => 'Unggah Dokumen']);
    }
}
