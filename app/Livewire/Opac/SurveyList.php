<?php

namespace App\Livewire\Opac;

use App\Models\Survey;
use Livewire\Component;

class SurveyList extends Component
{
    public function getSurveysProperty()
    {
        return Survey::active()
            ->with('branch:id,name')
            ->withCount(['sections', 'responses' => fn($q) => $q->where('is_complete', true)])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.opac.survey-list', [
            'surveys' => $this->surveys,
        ])->layout('components.opac.layout', [
            'title' => 'Survey Kepuasan',
        ]);
    }
}
