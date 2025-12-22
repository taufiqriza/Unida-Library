<?php

namespace App\Livewire\Staff\Survey;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Livewire\Component;
use Livewire\WithPagination;

class SurveyResponses extends Component
{
    use WithPagination;

    public Survey $survey;
    public string $search = '';
    public string $filterType = '';
    public ?int $selectedResponseId = null;

    public function mount(Survey $survey)
    {
        $this->survey = $survey->load(['sections.questions']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function viewResponse(int $id)
    {
        $this->selectedResponseId = $this->selectedResponseId === $id ? null : $id;
    }

    public function deleteResponse(int $id)
    {
        SurveyResponse::findOrFail($id)->delete();
        $this->survey->updateResponseCount();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Respons berhasil dihapus',
        ]);
    }

    public function getResponsesProperty()
    {
        $query = $this->survey->responses()
            ->where('is_complete', true)
            ->with(['answers', 'member:id,name,email']);
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('respondent_name', 'like', '%' . $this->search . '%')
                  ->orWhere('respondent_email', 'like', '%' . $this->search . '%')
                  ->orWhereHas('member', fn($m) => $m->where('name', 'like', '%' . $this->search . '%'));
            });
        }
        
        if ($this->filterType) {
            $query->where('respondent_type', $this->filterType);
        }
        
        return $query->orderByDesc('submitted_at')->paginate(15);
    }

    public function getSelectedResponseProperty()
    {
        if (!$this->selectedResponseId) return null;
        
        return SurveyResponse::with(['answers.question.section'])
            ->find($this->selectedResponseId);
    }

    public function render()
    {
        return view('livewire.staff.survey.survey-responses', [
            'responses' => $this->responses,
            'respondentTypes' => Survey::getRespondentTypes(),
            'selectedResponse' => $this->selectedResponse,
        ])->extends('staff.layouts.app')->section('content');
    }
}
