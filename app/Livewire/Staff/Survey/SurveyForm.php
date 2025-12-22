<?php

namespace App\Livewire\Staff\Survey;

use App\Models\Survey;
use App\Models\SurveySection;
use App\Models\SurveyQuestion;
use Livewire\Component;
use Illuminate\Support\Str;

class SurveyForm extends Component
{
    public ?Survey $survey = null;
    public bool $isEdit = false;

    // Form fields
    public string $title = '';
    public string $slug = '';
    public string $description = '';
    public string $status = 'draft';
    public ?string $start_date = null;
    public ?string $end_date = null;
    public bool $is_anonymous = true;
    public bool $require_login = false;
    public array $target_groups = [];

    // Sections & Questions
    public array $sections = [];

    // UI state
    public bool $showTemplateModal = false;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'slug' => 'nullable|alpha_dash|max:100',
        'description' => 'nullable|max:1000',
        'status' => 'required|in:draft,active,closed',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'is_anonymous' => 'boolean',
        'require_login' => 'boolean',
        'target_groups' => 'array',
        'sections' => 'required|array|min:1',
        'sections.*.name' => 'required|min:2|max:255',
        'sections.*.questions' => 'required|array|min:1',
        'sections.*.questions.*.text' => 'required|min:5',
        'sections.*.questions.*.type' => 'required|in:likert,text,select,rating,number',
    ];

    protected $messages = [
        'sections.required' => 'Survey harus memiliki minimal 1 bagian.',
        'sections.*.questions.required' => 'Setiap bagian harus memiliki minimal 1 pertanyaan.',
        'sections.*.questions.*.text.required' => 'Pertanyaan tidak boleh kosong.',
    ];

    public function mount(?Survey $survey = null)
    {
        if ($survey && $survey->exists) {
            $this->isEdit = true;
            $this->survey = $survey;
            $this->loadSurvey();
        } else {
            // Default empty section
            $this->sections = [
                $this->newSection()
            ];
        }
    }

    protected function loadSurvey(): void
    {
        $this->title = $this->survey->title;
        $this->slug = $this->survey->slug ?? '';
        $this->description = $this->survey->description ?? '';
        $this->status = $this->survey->status;
        $this->start_date = $this->survey->start_date?->format('Y-m-d');
        $this->end_date = $this->survey->end_date?->format('Y-m-d');
        $this->is_anonymous = $this->survey->is_anonymous;
        $this->require_login = $this->survey->require_login;
        $this->target_groups = $this->survey->target_groups ?? [];

        $this->sections = $this->survey->sections->map(function ($section) {
            return [
                'id' => $section->id,
                'name' => $section->name,
                'name_en' => $section->name_en,
                'description' => $section->description,
                'questions' => $section->questions->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'text' => $q->text,
                        'type' => $q->type,
                        'options' => $q->options ?? [],
                        'is_required' => $q->is_required,
                    ];
                })->toArray(),
            ];
        })->toArray();

        if (empty($this->sections)) {
            $this->sections = [$this->newSection()];
        }
    }

    protected function newSection(): array
    {
        return [
            'id' => null,
            'name' => '',
            'name_en' => '',
            'description' => '',
            'questions' => [
                $this->newQuestion()
            ],
        ];
    }

    protected function newQuestion(): array
    {
        return [
            'id' => null,
            'text' => '',
            'type' => 'likert',
            'options' => [],
            'is_required' => true,
        ];
    }

    public function addSection()
    {
        $this->sections[] = $this->newSection();
    }

    public function removeSection(int $index)
    {
        if (count($this->sections) > 1) {
            unset($this->sections[$index]);
            $this->sections = array_values($this->sections);
        }
    }

    public function addQuestion(int $sectionIndex)
    {
        $this->sections[$sectionIndex]['questions'][] = $this->newQuestion();
    }

    public function removeQuestion(int $sectionIndex, int $questionIndex)
    {
        if (count($this->sections[$sectionIndex]['questions']) > 1) {
            unset($this->sections[$sectionIndex]['questions'][$questionIndex]);
            $this->sections[$sectionIndex]['questions'] = array_values($this->sections[$sectionIndex]['questions']);
        }
    }

    public function moveSection(int $index, int $direction)
    {
        $newIndex = $index + $direction;
        if ($newIndex >= 0 && $newIndex < count($this->sections)) {
            $temp = $this->sections[$index];
            $this->sections[$index] = $this->sections[$newIndex];
            $this->sections[$newIndex] = $temp;
        }
    }

    public function moveQuestion(int $sectionIndex, int $questionIndex, int $direction)
    {
        $questions = &$this->sections[$sectionIndex]['questions'];
        $newIndex = $questionIndex + $direction;
        if ($newIndex >= 0 && $newIndex < count($questions)) {
            $temp = $questions[$questionIndex];
            $questions[$questionIndex] = $questions[$newIndex];
            $questions[$newIndex] = $temp;
        }
    }

    public function loadServqualTemplate()
    {
        $this->title = 'Survey Kepuasan Layanan Perpustakaan ' . date('Y');
        $this->description = 'Survey untuk mengukur tingkat kepuasan pemustaka terhadap layanan perpustakaan menggunakan dimensi SERVQUAL.';
        
        $this->sections = [
            [
                'id' => null,
                'name' => 'Tangible (Bukti Fisik)',
                'name_en' => 'Tangible',
                'description' => 'Aspek fasilitas fisik perpustakaan',
                'questions' => [
                    ['id' => null, 'text' => 'Fasilitas perpustakaan (meja, kursi, rak) bersih dan terawat.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Ruang perpustakaan nyaman untuk membaca dan belajar.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Peralatan layanan (komputer, OPAC, Wi-Fi) berfungsi dengan baik.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Koleksi tersusun rapi dan mudah dicari.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                ],
            ],
            [
                'id' => null,
                'name' => 'Reliability (Keandalan)',
                'name_en' => 'Reliability',
                'description' => 'Keandalan layanan perpustakaan',
                'questions' => [
                    ['id' => null, 'text' => 'Informasi yang diberikan pustakawan akurat dan dapat dipercaya.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Prosedur peminjaman/pengembalian berjalan sesuai aturan.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Koleksi yang dibutuhkan tersedia sesuai katalog.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                ],
            ],
            [
                'id' => null,
                'name' => 'Responsiveness (Daya Tanggap)',
                'name_en' => 'Responsiveness',
                'description' => 'Kecepatan dalam merespon kebutuhan pemustaka',
                'questions' => [
                    ['id' => null, 'text' => 'Pustakawan cepat merespon pertanyaan atau kebutuhan pemustaka.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Layanan diberikan tanpa menunda-nunda.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Pustakawan aktif menawarkan bantuan jika diperlukan.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                ],
            ],
            [
                'id' => null,
                'name' => 'Assurance (Jaminan)',
                'name_en' => 'Assurance',
                'description' => 'Pengetahuan dan kemampuan pustakawan',
                'questions' => [
                    ['id' => null, 'text' => 'Pustakawan memahami tugas dan prosedur layanan dengan baik.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Pustakawan memiliki pengetahuan yang memadai untuk menjawab pertanyaan.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Saya merasa aman dan nyaman saat berada di perpustakaan.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                ],
            ],
            [
                'id' => null,
                'name' => 'Empathy (Empati)',
                'name_en' => 'Empathy',
                'description' => 'Perhatian terhadap kebutuhan pemustaka',
                'questions' => [
                    ['id' => null, 'text' => 'Pustakawan memberikan perhatian kepada kebutuhan pemustaka.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Layanan dilakukan dengan sikap ramah dan peduli.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Perpustakaan menyediakan layanan sesuai kebutuhan kelompok khusus (per prodi).', 'type' => 'likert', 'options' => [], 'is_required' => true],
                ],
            ],
            [
                'id' => null,
                'name' => 'Penilaian Keseluruhan',
                'name_en' => 'Overall Assessment',
                'description' => 'Kepuasan secara menyeluruh',
                'questions' => [
                    ['id' => null, 'text' => 'Secara keseluruhan, saya puas dengan layanan perpustakaan UNIDA.', 'type' => 'likert', 'options' => [], 'is_required' => true],
                    ['id' => null, 'text' => 'Saran dan masukan untuk perpustakaan:', 'type' => 'text', 'options' => [], 'is_required' => false],
                ],
            ],
        ];

        $this->showTemplateModal = false;
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Template SERVQUAL berhasil dimuat',
        ]);
    }

    public function save()
    {
        $this->validate();

        $user = auth()->user();
        $survey = $this->isEdit ? $this->survey : new Survey();
        
        // Determine branch_id based on role
        $branchId = $this->isEdit 
            ? $survey->branch_id 
            : ($user->role === 'super_admin' ? null : $user->branch_id);
        
        $survey->fill([
            'branch_id' => $branchId,
            'title' => $this->title,
            'slug' => $this->slug ?: ($this->isEdit ? $survey->slug : Str::slug($this->title) . '-' . Str::random(6)),
            'description' => $this->description,
            'status' => $this->status,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'is_anonymous' => $this->is_anonymous,
            'require_login' => $this->require_login,
            'target_groups' => $this->target_groups,
            'created_by' => $this->isEdit ? $survey->created_by : auth()->id(),
        ]);
        
        $survey->save();

        // Sync sections
        $existingSectionIds = [];
        foreach ($this->sections as $sectionOrder => $sectionData) {
            $section = $sectionData['id'] 
                ? SurveySection::find($sectionData['id']) 
                : new SurveySection();
            
            $section->fill([
                'survey_id' => $survey->id,
                'name' => $sectionData['name'],
                'name_en' => $sectionData['name_en'] ?? null,
                'description' => $sectionData['description'] ?? null,
                'order' => $sectionOrder,
            ]);
            $section->save();
            $existingSectionIds[] = $section->id;

            // Sync questions
            $existingQuestionIds = [];
            foreach ($sectionData['questions'] as $questionOrder => $questionData) {
                $question = $questionData['id']
                    ? SurveyQuestion::find($questionData['id'])
                    : new SurveyQuestion();
                
                $question->fill([
                    'section_id' => $section->id,
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                    'options' => $questionData['options'] ?? null,
                    'min_value' => $questionData['type'] === 'likert' ? 1 : null,
                    'max_value' => $questionData['type'] === 'likert' ? 5 : null,
                    'is_required' => $questionData['is_required'] ?? true,
                    'order' => $questionOrder,
                ]);
                $question->save();
                $existingQuestionIds[] = $question->id;
            }

            // Delete removed questions
            SurveyQuestion::where('section_id', $section->id)
                ->whereNotIn('id', $existingQuestionIds)
                ->delete();
        }

        // Delete removed sections
        SurveySection::where('survey_id', $survey->id)
            ->whereNotIn('id', $existingSectionIds)
            ->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->isEdit ? 'Survey berhasil diperbarui' : 'Survey berhasil dibuat',
        ]);

        return redirect()->route('staff.survey.index');
    }

    public function render()
    {
        return view('livewire.staff.survey.survey-form', [
            'respondentTypes' => Survey::getRespondentTypes(),
            'questionTypes' => SurveyQuestion::getTypes(),
        ])->extends('staff.layouts.app')->section('content');
    }
}
