<?php

namespace App\Http\Livewire\Admin\Blog;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
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
        $category = Category::findOrFail($id);
        $category->delete();

        session()->flash('success', 'Categoria excluída com sucesso.');
    }

    public function render()
    {
        $categories = Category::query()
            ->withCount('posts')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.blog.category-index', [
            'categories' => $categories,
        ]);
    }
}