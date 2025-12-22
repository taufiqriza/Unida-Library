<?php

namespace App\Livewire\Staff\Survey;

use App\Models\Survey;
use App\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;

class SurveyDashboard extends Component
{
    use WithPagination;

    public string $tab = 'all';
    public string $search = '';

    protected $queryString = [
        'tab' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    // Helper methods for role checks
    public function isSuperAdmin(): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function isStaffOrLibrarian(): bool
    {
        return in_array(auth()->user()->role, ['staff', 'pustakawan']);
    }

    public function canCreate(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function canEdit(Survey $survey): bool
    {
        if ($this->isSuperAdmin()) return true;
        if ($this->isAdmin() && $survey->branch_id === auth()->user()->branch_id) return true;
        return false;
    }

    public function canViewDetails(Survey $survey): bool
    {
        if ($this->isSuperAdmin()) return true;
        return $survey->branch_id === auth()->user()->branch_id;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function deleteSurvey(int $id)
    {
        $survey = Survey::findOrFail($id);
        
        // Permission check
        if (!$this->canEdit($survey)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak memiliki akses']);
            return;
        }
        
        $survey->delete();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Survey berhasil dihapus',
        ]);
    }

    public function duplicateSurvey(int $id)
    {
        $survey = Survey::with('sections.questions')->findOrFail($id);
        
        // Permission check
        if (!$this->canEdit($survey)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak memiliki akses']);
            return;
        }
        
        $newSurvey = $survey->replicate();
        $newSurvey->title = $survey->title . ' (Copy)';
        $newSurvey->slug = null; // Will be auto-generated
        $newSurvey->status = 'draft';
        $newSurvey->response_count = 0;
        $newSurvey->save();
        
        // Copy sections and questions
        foreach ($survey->sections as $section) {
            $newSection = $section->replicate();
            $newSection->survey_id = $newSurvey->id;
            $newSection->save();
            
            foreach ($section->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->section_id = $newSection->id;
                $newQuestion->save();
            }
        }
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Survey berhasil diduplikasi',
        ]);
    }

    public function toggleStatus(int $id)
    {
        $survey = Survey::findOrFail($id);
        
        // Permission check
        if (!$this->canEdit($survey)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak memiliki akses']);
            return;
        }
        
        $newStatus = match($survey->status) {
            'draft' => 'active',
            'active' => 'closed',
            'closed' => 'draft',
        };
        
        $survey->update(['status' => $newStatus]);
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Status survey diubah menjadi ' . ucfirst($newStatus),
        ]);
    }

    public function getSurveysProperty()
    {
        $user = auth()->user();
        
        $query = Survey::query()
            ->withCount(['sections', 'responses' => fn($q) => $q->where('is_complete', true)])
            ->with(['creator:id,name', 'branch:id,name']);
        
        // Role-based filtering
        if ($this->isSuperAdmin()) {
            // Super admin can see all surveys
        } elseif ($this->isAdmin()) {
            // Admin can see all surveys (but limited actions on other branches)
        } else {
            // Staff/Pustakawan only see their branch surveys
            $query->where('branch_id', $user->branch_id);
        }
        
        // Filter by tab/status
        if ($this->tab === 'active') {
            $query->active();
        } elseif ($this->tab === 'draft') {
            $query->draft();
        } elseif ($this->tab === 'closed') {
            $query->closed();
        }
        
        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->latest()->paginate(10);
    }

    public function getStatsProperty(): array
    {
        $user = auth()->user();
        
        $baseQuery = Survey::query();
        
        // Role-based stats
        if (!$this->isSuperAdmin() && !$this->isAdmin()) {
            $baseQuery->where('branch_id', $user->branch_id);
        }
        
        return [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->active()->count(),
            'draft' => (clone $baseQuery)->draft()->count(),
            'closed' => (clone $baseQuery)->closed()->count(),
            'total_responses' => (clone $baseQuery)->sum('response_count'),
        ];
    }

    public function getBranchesProperty()
    {
        return Branch::orderBy('name')->get(['id', 'name']);
    }

    public function render()
    {
        return view('livewire.staff.survey.survey-dashboard', [
            'surveys' => $this->surveys,
            'stats' => $this->stats,
            'branches' => $this->branches,
        ])->extends('staff.layouts.app')->section('content');
    }
}
