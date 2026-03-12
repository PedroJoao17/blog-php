<?php

namespace App\Http\Livewire\Admin\Blog;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Blog\MediaService;
use App\Services\Blog\PostService;

class PostIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id, PostService $postService)
    {
        $post = Post::findOrFail($id);

        $postService->delete($post);

        session()->flash('success', 'Postagem excluída com sucesso.');
    }
    
    public function render()
    {
        $posts = Post::query()
            ->with(['author', 'category', 'media'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.blog.post-index', [
            'posts' => $posts,
        ]);
    }
}