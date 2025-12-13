<?php

namespace App\Livewire\Staff\Elibrary;

use App\Models\Ebook;
use Livewire\Component;
use Livewire\WithFileUploads;

class EbookForm extends Component
{
    use WithFileUploads;

    public ?Ebook $ebook = null;
    public $title = '', $sor = '', $publish_year = '', $isbn = '', $language = 'id';
    public $abstract = '', $classification = '', $call_number = '', $access_type = 'open';
    public $cover_image, $file_path, $is_active = true;
    public $existing_cover = '', $existing_file = '';

    public function mount($id = null)
    {
        if ($id) {
            $this->ebook = Ebook::findOrFail($id);
            $this->fill($this->ebook->only(['title', 'sor', 'publish_year', 'isbn', 'language', 'abstract', 'classification', 'call_number', 'access_type', 'is_active']));
            $this->existing_cover = $this->ebook->cover_image;
            $this->existing_file = $this->ebook->file_path;
        }
    }

    protected function rules()
    {
        return [
            'title' => 'required|max:500',
            'sor' => 'nullable|max:255',
            'publish_year' => 'nullable|digits:4',
            'cover_image' => 'nullable|image|max:2048',
            'file_path' => $this->ebook ? 'nullable|file|max:51200' : 'required|file|max:51200',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title, 'sor' => $this->sor, 'publish_year' => $this->publish_year,
            'isbn' => $this->isbn, 'language' => $this->language, 'abstract' => $this->abstract,
            'classification' => $this->classification, 'call_number' => $this->call_number,
            'access_type' => $this->access_type, 'is_active' => $this->is_active,
            'branch_id' => auth()->user()->branch_id, 'user_id' => auth()->id(),
        ];

        if ($this->cover_image) {
            $data['cover_image'] = $this->cover_image->store('ebooks/covers', 'public');
        }
        if ($this->file_path) {
            $data['file_path'] = $this->file_path->store('ebooks/files', 'public');
            $data['file_size'] = $this->file_path->getSize();
            $data['file_format'] = $this->file_path->getClientOriginalExtension();
        }

        if ($this->ebook) {
            $this->ebook->update($data);
        } else {
            Ebook::create($data);
        }

        session()->flash('success', 'E-Book berhasil disimpan');
        return redirect()->route('staff.elibrary.index', ['activeTab' => 'ebook']);
    }

    public function render()
    {
        return view('livewire.staff.elibrary.ebook-form', ['isEdit' => (bool) $this->ebook])
            ->extends('staff.layouts.app')->section('content');
    }
}
