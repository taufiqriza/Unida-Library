<?php

namespace App\Livewire\Staff\Task;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\Project;
use App\Models\Division;
use App\Models\User;
use Livewire\Component;

class TaskForm extends Component
{
    public ?Task $task = null;
    
    // Form fields
    public $title = '';
    public $description = '';
    public $project_id = '';
    public $division_id = '';
    public $status_id = '';
    public $priority = 'medium';
    public $type = 'general';
    public $assigned_to = '';
    public $start_date = '';
    public $due_date = '';
    public $estimated_hours = '';
    public $tags = '';

    protected function rules()
    {
        return [
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'division_id' => 'nullable|exists:divisions,id',
            'status_id' => 'required|exists:task_statuses,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'type' => 'required|in:general,collection,service,admin,event,task,bug,feature,improvement',
            'assigned_to' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ];
    }

    public function mount($task = null): void
    {
        $user = auth()->user();
        
        if ($task) {
            $this->task = Task::findOrFail($task);
            
            $this->title = $this->task->title;
            $this->description = $this->task->description;
            $this->project_id = $this->task->project_id;
            $this->division_id = $this->task->division_id;
            $this->status_id = $this->task->status_id;
            $this->priority = $this->task->priority;
            $this->type = $this->task->type;
            $this->assigned_to = $this->task->assigned_to;
            $this->start_date = $this->task->start_date?->format('Y-m-d');
            $this->due_date = $this->task->due_date?->format('Y-m-d');
            $this->estimated_hours = $this->task->estimated_hours;
            $this->tags = is_array($this->task->tags) ? implode(', ', $this->task->tags) : '';
        } else {
            // Defaults
            $this->status_id = TaskStatus::whereNull('project_id')->where('is_default', true)->first()?->id 
                ?? TaskStatus::whereNull('project_id')->orderBy('order')->first()?->id;
            $this->assigned_to = $user->id;
        }
    }

    public function save()
    {
        $validated = $this->validate();
        
        $user = auth()->user();
        
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'project_id' => $this->project_id ?: null,
            'division_id' => $this->division_id ?: null,
            'status_id' => $this->status_id,
            'priority' => $this->priority,
            'type' => $this->type,
            'assigned_to' => $this->assigned_to ?: null,
            'start_date' => $this->start_date ?: null,
            'due_date' => $this->due_date ?: null,
            'estimated_hours' => $this->estimated_hours ?: null,
            'tags' => $this->tags ? array_map('trim', explode(',', $this->tags)) : null,
        ];

        if ($this->task) {
            $this->task->update($data);
            session()->flash('success', 'Tugas berhasil diperbarui');
        } else {
            $data['branch_id'] = $user->branch_id;
            $data['reported_by'] = $user->id;
            Task::create($data);
            session()->flash('success', 'Tugas baru berhasil dibuat');
        }

        return redirect()->route('staff.task.index');
    }

    public function render()
    {
        $statuses = TaskStatus::whereNull('project_id')->orderBy('order')->get();
        if ($this->project_id) {
            $projectStatuses = TaskStatus::where('project_id', $this->project_id)->orderBy('order')->get();
            if ($projectStatuses->count() > 0) {
                $statuses = $projectStatuses;
            }
        }

        return view('livewire.staff.task.task-form', [
            'statuses' => $statuses,
            'projects' => Project::orderBy('name')->get(),
            'divisions' => Division::orderBy('name')->get(),
            'users' => User::whereIn('role', ['admin', 'super_admin', 'pustakawan'])->orderBy('name')->get(),
        ])->extends('staff.layouts.app')->section('content');
    }
}
