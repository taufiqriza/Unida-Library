<?php

namespace App\Livewire\Opac;

use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SurveyRespond extends Component
{
    public Survey $survey;
    public int $currentSection = 0;
    public array $answers = [];
    public bool $submitted = false;
    public bool $alreadySubmitted = false;

    // Respondent info
    public string $respondentType = '';
    public string $respondentName = '';
    public string $respondentEmail = '';
    public string $respondentFaculty = '';
    public string $respondentDepartment = '';

    protected function rules()
    {
        $rules = [];
        
        if (!$this->survey->is_anonymous || $this->survey->require_login) {
            $rules['respondentType'] = 'required';
            if (!Auth::guard('member')->check()) {
                $rules['respondentName'] = 'required|min:2';
            }
        }

        // Question validation
        foreach ($this->survey->sections as $section) {
            foreach ($section->questions as $question) {
                if ($question->is_required) {
                    $rules["answers.{$question->id}"] = 'required';
                }
            }
        }

        return $rules;
    }

    protected $messages = [
        'respondentType.required' => 'Pilih tipe responden.',
        'respondentName.required' => 'Nama wajib diisi.',
        'answers.*.required' => 'Pertanyaan ini wajib dijawab.',
    ];

    public function mount(string $slug)
    {
        $this->survey = Survey::where('slug', $slug)
            ->with(['sections.questions'])
            ->firstOrFail();
        
        // Check if survey is open
        if (!$this->survey->is_open) {
            abort(404, 'Survey tidak tersedia');
        }

        // Check if already submitted (by IP for anonymous)
        $ip = request()->ip();
        $existingResponse = SurveyResponse::where('survey_id', $this->survey->id)
            ->where('ip_address', $ip)
            ->where('is_complete', true)
            ->exists();
        
        if ($existingResponse) {
            $this->alreadySubmitted = true;
        }

        // Pre-fill from logged in member
        if (Auth::guard('member')->check()) {
            $member = Auth::guard('member')->user();
            $this->respondentName = $member->name;
            $this->respondentEmail = $member->email;
            $this->respondentFaculty = $member->faculty?->name ?? '';
            $this->respondentDepartment = $member->department?->name ?? '';
        }

        // Initialize answers
        foreach ($this->survey->sections as $section) {
            foreach ($section->questions as $question) {
                $this->answers[$question->id] = null;
            }
        }
    }

    public function nextSection()
    {
        // Validate current section
        $currentQuestions = $this->survey->sections[$this->currentSection]->questions;
        $errors = [];
        
        foreach ($currentQuestions as $question) {
            if ($question->is_required && empty($this->answers[$question->id])) {
                $errors["answers.{$question->id}"] = 'Pertanyaan ini wajib dijawab.';
            }
        }
        
        if (!empty($errors)) {
            foreach ($errors as $key => $message) {
                $this->addError($key, $message);
            }
            return;
        }
        
        $this->resetValidation();
        $this->currentSection++;
    }

    public function prevSection()
    {
        if ($this->currentSection > 0) {
            $this->currentSection--;
        }
    }

    public function submit()
    {
        $this->validate();

        // Create response
        $response = SurveyResponse::create([
            'survey_id' => $this->survey->id,
            'member_id' => Auth::guard('member')->id(),
            'respondent_type' => $this->respondentType,
            'respondent_name' => $this->respondentName,
            'respondent_email' => $this->respondentEmail,
            'respondent_faculty' => $this->respondentFaculty,
            'respondent_department' => $this->respondentDepartment,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Save answers
        foreach ($this->answers as $questionId => $answer) {
            if ($answer !== null && $answer !== '') {
                $question = $this->survey->sections
                    ->flatMap->questions
                    ->firstWhere('id', $questionId);
                
                SurveyAnswer::create([
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                    'answer' => is_numeric($answer) ? null : $answer,
                    'score' => is_numeric($answer) ? (int) $answer : null,
                ]);
            }
        }

        $response->markComplete();
        $this->submitted = true;
    }

    public function getProgressProperty(): int
    {
        $totalSections = $this->survey->sections->count();
        return $totalSections > 0 ? round(($this->currentSection / $totalSections) * 100) : 0;
    }

    public function render()
    {
        return view('livewire.opac.survey-respond', [
            'respondentTypes' => Survey::getRespondentTypes(),
            'likertLabels' => \App\Models\SurveyQuestion::getLikertLabels(),
        ])->layout('components.opac.layout', [
            'title' => $this->survey->title,
        ]);
    }
}
