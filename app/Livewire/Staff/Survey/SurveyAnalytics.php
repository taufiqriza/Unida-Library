<?php

namespace App\Livewire\Staff\Survey;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Livewire\Component;

class SurveyAnalytics extends Component
{
    public Survey $survey;
    public string $filterType = '';
    public array $analytics = [];

    public function mount(Survey $survey)
    {
        $this->survey = $survey->load(['sections.questions', 'responses.answers']);
        $this->calculateAnalytics();
    }

    public function updatedFilterType()
    {
        $this->calculateAnalytics();
    }

    protected function calculateAnalytics(): void
    {
        $responses = $this->survey->responses()
            ->where('is_complete', true)
            ->when($this->filterType, fn($q) => $q->where('respondent_type', $this->filterType))
            ->with('answers')
            ->get();

        if ($responses->isEmpty()) {
            $this->analytics = [
                'sections' => [],
                'questions' => [],
                'overall' => null,
                'response_count' => 0,
                'by_type' => [],
            ];
            return;
        }

        $sectionScores = [];
        $questionScores = [];
        
        foreach ($this->survey->sections as $section) {
            $questionIds = $section->questions->pluck('id')->toArray();
            $maxPerQuestion = 5; // Likert max
            $maxSectionScore = count($questionIds) * $maxPerQuestion;
            
            $sectionTotal = 0;
            $sectionResponses = 0;
            
            foreach ($section->questions as $question) {
                $qScores = [];
                $qTotal = 0;
                $qCount = 0;
                
                foreach ($responses as $response) {
                    $answer = $response->answers->firstWhere('question_id', $question->id);
                    if ($answer && $answer->score !== null) {
                        $qTotal += $answer->score;
                        $qCount++;
                        $qScores[] = $answer->score;
                    }
                }
                
                $qAvg = $qCount > 0 ? $qTotal / $qCount : 0;
                $qPercentage = ($qAvg / $maxPerQuestion) * 100;
                
                $questionScores[$question->id] = [
                    'id' => $question->id,
                    'section_id' => $section->id,
                    'text' => $question->text,
                    'type' => $question->type,
                    'avg_score' => round($qAvg, 2),
                    'max_score' => $maxPerQuestion,
                    'percentage' => round($qPercentage, 1),
                    'category' => Survey::getCategory($qPercentage),
                    'response_count' => $qCount,
                    'distribution' => $this->getScoreDistribution($qScores),
                ];
                
                $sectionTotal += $qAvg;
                if ($qCount > 0) $sectionResponses++;
            }
            
            $sectionAvg = $sectionResponses > 0 ? $sectionTotal : 0;
            $sectionPercentage = $maxSectionScore > 0 ? ($sectionAvg / count($section->questions) / $maxPerQuestion) * 100 : 0;
            
            $sectionScores[$section->id] = [
                'id' => $section->id,
                'name' => $section->name,
                'name_en' => $section->name_en,
                'avg_score' => round($sectionAvg, 2),
                'max_score' => count($section->questions) * $maxPerQuestion,
                'percentage' => round($sectionPercentage, 1),
                'category' => Survey::getCategory($sectionPercentage),
                'question_count' => count($section->questions),
            ];
        }

        // Overall
        $overallScores = array_column($sectionScores, 'percentage');
        $overallPercentage = count($overallScores) > 0 ? array_sum($overallScores) / count($overallScores) : 0;

        // By respondent type
        $byType = $this->survey->responses()
            ->where('is_complete', true)
            ->selectRaw('respondent_type, COUNT(*) as count')
            ->groupBy('respondent_type')
            ->pluck('count', 'respondent_type')
            ->toArray();

        $this->analytics = [
            'sections' => $sectionScores,
            'questions' => $questionScores,
            'overall' => [
                'percentage' => round($overallPercentage, 1),
                'category' => Survey::getCategory($overallPercentage),
            ],
            'response_count' => $responses->count(),
            'by_type' => $byType,
        ];
    }

    protected function getScoreDistribution(array $scores): array
    {
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($scores as $score) {
            if (isset($distribution[$score])) {
                $distribution[$score]++;
            }
        }
        return $distribution;
    }

    public function render()
    {
        return view('livewire.staff.survey.survey-analytics', [
            'respondentTypes' => Survey::getRespondentTypes(),
            'likertLabels' => SurveyQuestion::getLikertLabels(),
        ])->extends('staff.layouts.app')->section('content');
    }
}
