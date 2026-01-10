<?php

namespace App\Livewire\Staff\Task;

use App\Models\PersonalNote;
use Livewire\Component;

class PersonalNotes extends Component
{
    public $notes;
    public $showModal = false;
    public $editMode = false;
    public $noteId = null;
    
    // Form fields
    public $title = '';
    public $content = '';
    public $category = 'general';
    public $color = 'gray';
    
    public $searchQuery = '';
    public $filterCategory = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'nullable|string',
        'category' => 'required|string',
        'color' => 'required|string',
    ];

    public function mount()
    {
        $this->loadNotes();
    }

    public function loadNotes()
    {
        $query = PersonalNote::where('user_id', auth()->id())
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('updated_at');
        
        if ($this->searchQuery) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('content', 'like', '%' . $this->searchQuery . '%');
            });
        }
        
        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }
        
        $this->notes = $query->get();
    }

    public function updatedSearchQuery()
    {
        $this->loadNotes();
    }

    public function updatedFilterCategory()
    {
        $this->loadNotes();
    }

    public function openCreateModal()
    {
        $this->reset(['noteId', 'title', 'content', 'category', 'color']);
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $note = PersonalNote::where('user_id', auth()->id())->findOrFail($id);
        $this->noteId = $note->id;
        $this->title = $note->title;
        $this->content = $note->content;
        $this->category = $note->category;
        $this->color = $note->color;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['noteId', 'title', 'content', 'category', 'color']);
    }

    public function save()
    {
        $this->validate();
        
        if ($this->editMode && $this->noteId) {
            $note = PersonalNote::where('user_id', auth()->id())->findOrFail($this->noteId);
            $note->update([
                'title' => $this->title,
                'content' => $this->content,
                'category' => $this->category,
                'color' => $this->color,
            ]);
        } else {
            PersonalNote::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'content' => $this->content,
                'category' => $this->category,
                'color' => $this->color,
            ]);
        }
        
        $this->closeModal();
        $this->loadNotes();
    }

    public function togglePin($id)
    {
        $note = PersonalNote::where('user_id', auth()->id())->findOrFail($id);
        $note->update([
            'is_pinned' => !$note->is_pinned,
            'pinned_at' => $note->is_pinned ? null : now(),
        ]);
        $this->loadNotes();
    }

    public function delete($id)
    {
        PersonalNote::where('user_id', auth()->id())->where('id', $id)->delete();
        $this->loadNotes();
    }

    public function render()
    {
        return view('livewire.staff.task.personal-notes', [
            'categories' => PersonalNote::getCategories(),
            'colors' => PersonalNote::getColors(),
        ]);
    }
}
