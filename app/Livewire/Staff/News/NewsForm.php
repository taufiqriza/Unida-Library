<?php

namespace App\Livewire\Staff\News;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class NewsForm extends Component
{
    use WithFileUploads;

    public ?News $news = null;
    public $title = '';
    public $slug = '';
    public $news_category_id = '';
    public $excerpt = '';
    public $content = '';
    public $featured_image;
    public $existing_image = '';
    public $status = 'draft';
    public $is_featured = false;
    public $is_pinned = false;

    public function mount($id = null)
    {
        if ($id) {
            $this->news = News::where('branch_id', auth()->user()->branch_id)->findOrFail($id);
            $this->title = $this->news->title;
            $this->slug = $this->news->slug;
            $this->news_category_id = $this->news->news_category_id;
            $this->excerpt = $this->news->excerpt;
            $this->content = $this->news->content;
            $this->existing_image = $this->news->featured_image;
            $this->status = $this->news->status;
            $this->is_featured = $this->news->is_featured;
            $this->is_pinned = $this->news->is_pinned;
        }
    }

    public function updatedTitle($value)
    {
        if (!$this->news) {
            $this->slug = Str::slug($value);
        }
    }

    protected function rules()
    {
        return [
            'title' => 'required|max:255',
            'slug' => 'required|max:255|unique:news,slug,' . ($this->news?->id ?? 'NULL'),
            'news_category_id' => 'nullable|exists:news_categories,id',
            'excerpt' => 'nullable|max:500',
            'content' => 'required',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'news_category_id' => $this->news_category_id ?: null,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_pinned' => $this->is_pinned,
            'branch_id' => auth()->user()->branch_id,
            'user_id' => auth()->id(),
        ];

        if ($this->status === 'published' && (!$this->news || $this->news->status !== 'published')) {
            $data['published_at'] = now();
        }

        if ($this->featured_image) {
            $data['featured_image'] = $this->featured_image->store('news', 'public');
        }

        if ($this->news) {
            $this->news->update($data);
            $message = 'Berita berhasil diperbarui';
        } else {
            News::create($data);
            $message = 'Berita berhasil dibuat';
        }

        session()->flash('success', $message);
        return redirect()->route('staff.news.index');
    }

    public function render()
    {
        return view('livewire.staff.news.news-form', [
            'categories' => NewsCategory::where('is_active', true)->pluck('name', 'id'),
            'isEdit' => (bool) $this->news,
        ])->extends('staff.layouts.app')->section('content');
    }
}
