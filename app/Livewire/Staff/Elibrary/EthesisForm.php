<?php

namespace App\Livewire\Staff\Elibrary;

use App\Models\Department;
use App\Models\Ethesis;
use Livewire\Component;
use Livewire\WithFileUploads;

class EthesisForm extends Component
{
    use WithFileUploads;

    public ?Ethesis $ethesis = null;
    public $title = '', $author = '', $nim = '', $department_id = '', $year = '', $type = 'skripsi';
    public $abstract = '', $advisor1 = '', $advisor2 = '', $keywords = '';
    public $is_public = true, $is_fulltext_public = false;
    public $cover_path, $file_path;
    public $existing_cover = '', $existing_file = '';

    public function mount($id = null)
    {
        if ($id) {
            $this->ethesis = Ethesis::findOrFail($id);
            $this->fill($this->ethesis->only(['title', 'author', 'nim', 'department_id', 'year', 'type', 'abstract', 'advisor1', 'advisor2', 'keywords', 'is_public', 'is_fulltext_public']));
            $this->existing_cover = $this->ethesis->cover_path;
            $this->existing_file = $this->ethesis->file_path;
        } else {
            $this->year = date('Y');
        }
    }

    protected function rules()
    {
        return [
            'title' => 'required|max:500',
            'author' => 'required|max:255',
            'nim' => 'required|max:50',
            'department_id' => 'required|exists:departments,id',
            'year' => 'required|digits:4',
            'type' => 'required|in:skripsi,tesis,disertasi',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title, 'author' => $this->author, 'nim' => $this->nim,
            'department_id' => $this->department_id, 'year' => $this->year, 'type' => $this->type,
            'abstract' => $this->abstract, 'advisor1' => $this->advisor1, 'advisor2' => $this->advisor2,
            'keywords' => $this->keywords, 'is_public' => $this->is_public, 'is_fulltext_public' => $this->is_fulltext_public,
            'branch_id' => auth()->user()->branch_id, 'user_id' => auth()->id(),
        ];

        if ($this->cover_path) {
            $data['cover_path'] = $this->cover_path->store('ethesis/covers', 'public');
        }
        if ($this->file_path) {
            $data['file_path'] = $this->file_path->store('ethesis/files', 'public');
        }

        if ($this->ethesis) {
            $this->ethesis->update($data);
        } else {
            Ethesis::create($data);
        }

        session()->flash('success', 'E-Thesis berhasil disimpan');
        return redirect()->route('staff.elibrary.index', ['activeTab' => 'ethesis']);
    }

    public function render()
    {
        return view('livewire.staff.elibrary.ethesis-form', [
            'isEdit' => (bool) $this->ethesis,
            'departments' => Department::orderBy('name')->pluck('name', 'id'),
        ])->extends('staff.layouts.app')->section('content');
    }
}
