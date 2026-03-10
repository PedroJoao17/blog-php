<?php

namespace App\Http\Livewire\Admin\Blog;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CategoryForm extends Component
{
    public $categoryId = null;
    public $name = '';
    public $slug = '';
    public $slugTouched = false;

    public function mount($category = null)
    {
        if ($category) {
            $category = Category::findOrFail($category);

            $this->categoryId = $category->id;
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->slugTouched = true;
        }
    }

    public function updatedName($value)
    {
        if (!$this->slugTouched) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSlug()
    {
        $this->slugTouched = true;
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->name);
        $this->slugTouched = true;
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_categories', 'slug')->ignore($this->categoryId),
            ],
        ];
    }

    protected $messages = [
        'name.required' => 'O nome é obrigatório.',
        'slug.required' => 'O slug é obrigatório.',
        'slug.unique' => 'Este slug já está em uso.',
    ];

    public function save()
    {
        $data = $this->validate();

        $category = Category::updateOrCreate(
            ['id' => $this->categoryId],
            $data
        );

        session()->flash('success', 'Categoria salva com sucesso.');

        return redirect()->route('admin.blog.categories.edit', $category->id);
    }

    public function render()
    {
        return view('livewire.admin.blog.category-form');
    }
}