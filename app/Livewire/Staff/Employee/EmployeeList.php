<?php

namespace App\Livewire\Staff\Employee;

use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $faculty = '';
    public string $status = 'active';
    public int $perPage = 20;

    protected $queryString = ['search', 'type', 'faculty', 'status'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Employee::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('niy', 'like', "%{$this->search}%")
                  ->orWhere('nidn', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->faculty) {
            $query->where('faculty', $this->faculty);
        }

        if ($this->status === 'active') {
            $query->where('is_active', true);
        } elseif ($this->status === 'inactive') {
            $query->where('is_active', false);
        }

        $employees = $query->orderBy('name')->paginate($this->perPage);

        // Get faculties for filter
        $faculties = Employee::whereNotNull('faculty')
            ->distinct()
            ->pluck('faculty')
            ->sort();

        // Stats
        $stats = [
            'total' => Employee::count(),
            'dosen' => Employee::dosen()->active()->count(),
            'tendik' => Employee::tendik()->count(),
            'with_email' => Employee::whereNotNull('email')->count(),
        ];

        return view('livewire.staff.employee.employee-list', [
            'employees' => $employees,
            'faculties' => $faculties,
            'stats' => $stats,
        ])->layout('staff.layouts.app', ['title' => 'Data SDM']);
    }
}
