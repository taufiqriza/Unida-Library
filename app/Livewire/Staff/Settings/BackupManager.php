<?php

namespace App\Livewire\Staff\Settings;

use Livewire\Component;
use App\Models\Backup;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupManager extends Component
{
    public $backups = [];
    public $branches = [];
    public $selectedBranch = '';
    public $backupType = 'branch';
    public $backupName = '';
    public $description = '';
    public $showCreateModal = false;
    public $showDetailModal = false;
    public $selectedBackup = null;
    public $isCreating = false;

    protected $rules = [
        'backupName' => 'required|string|max:255',
        'backupType' => 'required|in:full,branch,partial',
        'selectedBranch' => 'required_if:backupType,branch',
        'description' => 'nullable|string|max:500'
    ];

    public function mount()
    {
        $this->loadBackups();
        $this->loadBranches();
    }

    public function loadBackups()
    {
        $query = Backup::with(['branch', 'creator'])
            ->orderBy('created_at', 'desc');

        // Filter by branch if user is admin (not super_admin)
        if (auth()->user()->role === 'admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $this->backups = $query->get();
    }

    public function loadBranches()
    {
        if (auth()->user()->role === 'super_admin') {
            $this->branches = Branch::orderBy('name')->get();
        } else {
            $this->branches = Branch::where('id', auth()->user()->branch_id)->get();
            $this->selectedBranch = auth()->user()->branch_id;
        }
    }

    public function createBackup()
    {
        $this->validate();
        $this->isCreating = true;

        try {
            $backup = Backup::create([
                'name' => $this->backupName,
                'type' => $this->backupType,
                'branch_id' => $this->backupType === 'branch' ? $this->selectedBranch : null,
                'created_by' => auth()->id(),
                'description' => $this->description,
                'status' => 'pending',
                'tables_included' => [],
                'data_counts' => [],
                'total_records' => 0,
                'file_size' => '0',
                'file_paths' => [],
                'checksum' => '',
            ]);

            // Process backup in background
            $this->processBackup($backup);

            $this->resetForm();
            $this->loadBackups();
            
            session()->flash('message', 'Backup berhasil dibuat dan sedang diproses.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat backup: ' . $e->getMessage());
        }

        $this->isCreating = false;
    }

    private function processBackup(Backup $backup)
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupDir = storage_path('app/backups/' . $backup->id);
            
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $tables = $this->getBackupTables($backup->type, $backup->branch_id);
            $filePaths = [];
            $dataCounts = [];
            $totalRecords = 0;
            $totalSize = 0;

            foreach ($tables as $table => $query) {
                try {
                    $data = DB::select($query);
                    $count = count($data);
                    
                    // Always record the count, even if 0
                    $dataCounts[$table] = $count;
                    $totalRecords += $count;
                    
                    if ($count > 0) {
                        $fileName = "{$table}_{$timestamp}.json";
                        $filePath = $backupDir . '/' . $fileName;
                        
                        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
                        
                        $filePaths[] = $filePath;
                        $totalSize += filesize($filePath);
                    } else {
                        // Create empty file for tables with no data
                        $fileName = "{$table}_{$timestamp}.json";
                        $filePath = $backupDir . '/' . $fileName;
                        file_put_contents($filePath, json_encode([], JSON_PRETTY_PRINT));
                        $filePaths[] = $filePath;
                        $totalSize += filesize($filePath);
                    }
                } catch (\Exception $tableError) {
                    // Log table-specific errors but continue with other tables
                    $dataCounts[$table] = 0;
                    \Log::warning("Backup table error for {$table}: " . $tableError->getMessage());
                }
            }

            // Create summary file
            $summary = [
                'backup_id' => $backup->id,
                'name' => $backup->name,
                'type' => $backup->type,
                'branch_id' => $backup->branch_id,
                'branch_name' => $backup->branch?->name ?? 'All Branches',
                'created_at' => $backup->created_at,
                'tables' => array_keys($tables),
                'data_counts' => $dataCounts,
                'total_records' => $totalRecords,
                'file_size' => $totalSize,
                'queries_used' => $tables // For debugging
            ];

            $summaryPath = $backupDir . '/backup_summary.json';
            file_put_contents($summaryPath, json_encode($summary, JSON_PRETTY_PRINT));
            $filePaths[] = $summaryPath;
            $totalSize += filesize($summaryPath);

            // Generate checksum
            $checksum = md5(serialize($dataCounts) . $totalRecords);

            // Update backup record
            $backup->update([
                'tables_included' => array_keys($tables),
                'data_counts' => $dataCounts,
                'total_records' => $totalRecords,
                'file_size' => $totalSize,
                'file_paths' => $filePaths,
                'checksum' => $checksum,
                'status' => 'completed',
                'completed_at' => now(),
                'metadata' => [
                    'queries_executed' => count($tables),
                    'files_created' => count($filePaths),
                    'backup_duration' => now()->diffInSeconds($backup->created_at) . ' seconds'
                ]
            ]);

        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            \Log::error("Backup failed for backup ID {$backup->id}: " . $e->getMessage());
        }
    }

    private function getBackupTables($type, $branchId = null)
    {
        $tables = [];

        switch ($type) {
            case 'full':
                $tables = [
                    'books' => 'SELECT * FROM books',
                    'items' => 'SELECT * FROM items',
                    'members' => 'SELECT * FROM members',
                    'loans' => 'SELECT * FROM loans',
                    'book_author' => 'SELECT * FROM book_author',
                    'book_subject' => 'SELECT * FROM book_subject',
                    'authors' => 'SELECT * FROM authors',
                    'subjects' => 'SELECT * FROM subjects',
                ];
                break;

            case 'branch':
                $tables = [
                    'books' => "SELECT * FROM books WHERE branch_id = {$branchId}",
                    'items' => "SELECT i.* FROM items i JOIN books b ON i.book_id = b.id WHERE b.branch_id = {$branchId}",
                    'members' => "SELECT * FROM members WHERE branch_id = {$branchId}",
                    'loans' => "SELECT * FROM loans WHERE branch_id = {$branchId}",
                    'book_author' => "SELECT ba.* FROM book_author ba JOIN books b ON ba.book_id = b.id WHERE b.branch_id = {$branchId}",
                    'book_subject' => "SELECT bs.* FROM book_subject bs JOIN books b ON bs.book_id = b.id WHERE b.branch_id = {$branchId}",
                    'authors' => "SELECT DISTINCT a.* FROM authors a JOIN book_author ba ON a.id = ba.author_id JOIN books b ON ba.book_id = b.id WHERE b.branch_id = {$branchId}",
                    'subjects' => "SELECT DISTINCT s.* FROM subjects s JOIN book_subject bs ON s.id = bs.subject_id JOIN books b ON bs.book_id = b.id WHERE b.branch_id = {$branchId}",
                ];
                break;

            case 'partial':
                $tables = [
                    'books' => $branchId ? "SELECT * FROM books WHERE branch_id = {$branchId}" : 'SELECT * FROM books',
                    'items' => $branchId ? "SELECT i.* FROM items i JOIN books b ON i.book_id = b.id WHERE b.branch_id = {$branchId}" : 'SELECT * FROM items',
                    'members' => $branchId ? "SELECT * FROM members WHERE branch_id = {$branchId}" : 'SELECT * FROM members',
                ];
                break;
        }

        return $tables;
    }

    public function showDetail($backupId)
    {
        $this->selectedBackup = Backup::with(['branch', 'creator'])->find($backupId);
        $this->showDetailModal = true;
    }

    public function deleteBackup($backupId)
    {
        try {
            $backup = Backup::find($backupId);
            
            // Delete files
            foreach ($backup->file_paths as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Delete directory if empty
            $backupDir = dirname($backup->file_paths[0] ?? '');
            if (file_exists($backupDir) && count(scandir($backupDir)) <= 2) {
                rmdir($backupDir);
            }
            
            $backup->delete();
            $this->loadBackups();
            
            session()->flash('message', 'Backup berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus backup: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->backupName = '';
        $this->description = '';
        $this->showCreateModal = false;
    }

    public function render()
    {
        return view('livewire.staff.settings.backup-manager');
    }
}
