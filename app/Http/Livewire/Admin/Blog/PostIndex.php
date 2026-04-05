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
            ->with(['author', 'category', 'categories', 'tags', 'media'])
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', $search)
                        ->orWhere('slug', 'like', $search)
                        ->orWhere('status', 'like', $search)
                        ->orWhereHas('categories', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', $search);
                        })
                        ->orWhereHas('tags', function ($tagQuery) use ($search) {
                            $tagQuery->where('name', 'like', $search);
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.blog.post-index', [
            'posts' => $posts,
        ]);
    }
}