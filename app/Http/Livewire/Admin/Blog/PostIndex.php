<?php

namespace App\Http\Livewire\Admin\Blog;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class PostIndex extends Component
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
        $post = Post::findOrFail($id);

        foreach ($post->media as $media) {
            if ($media->disk && $media->path && Storage::disk($media->disk)->exists($media->path)) {
                Storage::disk($media->disk)->delete($media->path);
            }

            $media->delete();
        }

        $post->delete();

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