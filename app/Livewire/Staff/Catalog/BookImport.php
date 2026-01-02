<?php

namespace App\Livewire\Staff\Catalog;

use App\Models\Branch;
use App\Models\ImportBatch;
use App\Services\BookImportService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class BookImport extends Component
{
    use WithFileUploads;

    public $excelFile;
    public $coversZip;
    public $branchId;
    
    public ?ImportBatch $batch = null;
    public array $preview = [];
    public array $stats = [];
    
    public string $filterStatus = 'all';
    public string $search = '';
    public bool $includeWarnings = true;
    
    public bool $showDetailModal = false;
    public ?array $selectedRow = null;
    public bool $isUploading = false;
    
    // Import result
    public bool $showSuccessModal = false;
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public bool $showErrorModal = false;
    public string $errorMessage = '';

    protected $rules = [
        'excelFile' => 'required|file|mimes:xlsx,xls|max:10240',
        'coversZip' => 'nullable|file|mimes:zip|max:102400',
        'branchId' => 'required|exists:branches,id',
    ];

    public function updatedExcelFile()
    {
        $this->validateOnly('excelFile');
    }

    public function mount()
    {
        $user = Auth::user();
        $this->branchId = $user->branch_id ?? Branch::first()?->id;
    }

    public function downloadTemplate()
    {
        $branch = Branch::findOrFail($this->branchId);
        $service = app(BookImportService::class);
        $path = $service->generateTemplate($branch);
        
        return response()->download($path)->deleteFileAfterSend();
    }

    public function processUpload()
    {
        $this->validate();

        $user = Auth::user();
        
        // Create batch
        $this->batch = ImportBatch::create([
            'branch_id' => $this->branchId,
            'user_id' => $user->id,
            'type' => 'books',
            'filename' => $this->excelFile->getClientOriginalName(),
            'status' => 'validating',
        ]);

        // Parse and validate
        $service = app(BookImportService::class);
        $result = $service->parseAndValidate(
            $this->batch,
            $this->excelFile,
            $this->coversZip
        );

        $this->stats = $result['stats'];
        $this->preview = $result['preview'];

        $this->dispatch('upload-complete');
    }

    public function executeImport()
    {
        if (!$this->batch) return;

        $service = app(BookImportService::class);
        $result = $service->executeImport($this->batch, $this->includeWarnings);

        if ($result['success']) {
            $this->importedCount = $result['imported'];
            $this->skippedCount = $result['skipped'];
            $this->showSuccessModal = true;
        } else {
            $this->errorMessage = $result['error'];
            $this->showErrorModal = true;
        }
    }

    public function cancelImport()
    {
        if ($this->batch) {
            $this->batch->delete();
        }
        
        $this->reset(['batch', 'preview', 'stats', 'excelFile', 'coversZip']);
    }

    public function showDetail(int $index)
    {
        $this->selectedRow = $this->preview[$index] ?? null;
        $this->showDetailModal = true;
    }

    public function getFilteredPreviewProperty(): array
    {
        $filtered = $this->preview;

        if ($this->filterStatus !== 'all') {
            $filtered = array_filter($filtered, fn($row) => $row['status'] === $this->filterStatus);
        }

        if ($this->search) {
            $search = strtolower($this->search);
            $filtered = array_filter($filtered, fn($row) => 
                str_contains(strtolower($row['data']['title'] ?? ''), $search) ||
                str_contains(strtolower($row['data']['authors'] ?? ''), $search)
            );
        }

        return array_values($filtered);
    }

    public function getBranchesProperty()
    {
        return Branch::where('is_active', true)->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.staff.catalog.book-import', [
            'filteredPreview' => $this->filteredPreview,
            'branches' => $this->branches,
        ])->extends('staff.layouts.app')->section('content');
    }
}
