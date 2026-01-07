<?php

namespace App\Livewire\Opac;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class FacultyDirectory extends Component
{
    public $search = '';
    public $selectedRank = '';
    public $selectedFaculty = '';
    public $selectedDepartment = '';
    public $viewMode = 'grid'; // grid or list
    
    public $facultyData = [];
    public $ranks = [];
    public $faculties = [];
    public $departments = [];
    
    public function mount()
    {
        try {
            $this->loadFacultyData();
            $this->extractFilters();
        } catch (\Exception $e) {
            // Log error but don't break the page
            \Log::error('Faculty Directory Error: ' . $e->getMessage());
            $this->facultyData = [];
            $this->ranks = [];
            $this->faculties = [];
            $this->departments = [];
        }
    }
    
    private function loadFacultyData()
    {
        $csvPath = storage_path('app/import/dosen_photos_list.csv');
        
        if (!file_exists($csvPath)) {
            $this->facultyData = [];
            return;
        }
        
        try {
            $csvContent = file($csvPath);
            if (!$csvContent) {
                $this->facultyData = [];
                return;
            }
            
            $csv = array_map('str_getcsv', $csvContent);
            $headers = array_shift($csv);
            
            foreach ($csv as $row) {
                if (count($row) >= 6 && !empty($row[0])) {
                    $fileName = $row[0];
                    $this->facultyData[] = [
                        'name' => $this->extractName($fileName),
                        'rank' => $this->extractRank($fileName),
                        'faculty' => $this->extractFaculty($row[5] ?? ''),
                        'department' => $this->extractDepartment($row[5] ?? ''),
                        'file_id' => $row[1] ?? '',
                        'direct_url' => $row[2] ?? '',
                        'thumbnail_url' => $row[3] ?? '',
                        'folder_path' => $row[5] ?? '',
                        'original_filename' => $fileName
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('CSV parsing error: ' . $e->getMessage());
            $this->facultyData = [];
        }
    }
    
    private function extractName($filename)
    {
        // Remove extension and clean up
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = preg_replace('/^\d+\.\s*/', '', $name); // Remove numbering
        return trim($name);
    }
    
    private function extractRank($filename)
    {
        if (preg_match('/(Prof\.|Dr\.|K\.H\.|Drs\.|M\.A|M\.Ec|Lc\.|S\.Ag)/i', $filename, $matches)) {
            return $matches[1];
        }
        return 'Dosen';
    }
    
    private function extractFaculty($folderPath)
    {
        if (strpos($folderPath, 'Rektor') !== false) return 'Rektorat';
        if (strpos($folderPath, 'Presiden') !== false) return 'Kepresidenan';
        return 'Fakultas';
    }
    
    private function extractDepartment($folderPath)
    {
        $parts = explode('/', $folderPath);
        return end($parts);
    }
    
    private function extractFilters()
    {
        if (empty($this->facultyData)) {
            $this->ranks = [];
            $this->faculties = [];
            $this->departments = [];
            return;
        }
        
        $this->ranks = collect($this->facultyData)->pluck('rank')->unique()->filter()->sort()->values()->toArray();
        $this->faculties = collect($this->facultyData)->pluck('faculty')->unique()->filter()->sort()->values()->toArray();
        $this->departments = collect($this->facultyData)->pluck('department')->unique()->filter()->sort()->values()->toArray();
    }
    
    public function getFilteredDataProperty()
    {
        $data = collect($this->facultyData);
        
        if ($this->search) {
            $data = $data->filter(function ($item) {
                return stripos($item['name'], $this->search) !== false;
            });
        }
        
        if ($this->selectedRank) {
            $data = $data->filter(fn($item) => $item['rank'] === $this->selectedRank);
        }
        
        if ($this->selectedFaculty) {
            $data = $data->filter(fn($item) => $item['faculty'] === $this->selectedFaculty);
        }
        
        if ($this->selectedDepartment) {
            $data = $data->filter(fn($item) => $item['department'] === $this->selectedDepartment);
        }
        
        return $data->sortBy('name')->values();
    }
    
    public function clearFilters()
    {
        $this->search = '';
        $this->selectedRank = '';
        $this->selectedFaculty = '';
        $this->selectedDepartment = '';
    }
    
    public function render()
    {
        return view('livewire.opac.faculty-directory')
            ->layout('components.opac.layout', [
                'title' => 'Direktori Akademisi UNIDA Gontor',
                'description' => 'Direktori lengkap dosen dan akademisi Universitas Darussalam Gontor'
            ]);
    }
}
