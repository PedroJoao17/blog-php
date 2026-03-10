<?php

namespace App\Http\Livewire\Admin\Blog;

use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class TagIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        session()->flash('success', 'Tag excluída com sucesso.');
    }

    public function render()
    {
        $tags = Tag::query()
            ->withCount('posts')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.blog.tag-index', [
            'tags' => $tags,
        ]);
    }
}