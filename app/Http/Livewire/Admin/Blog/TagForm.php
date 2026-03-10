<?php

namespace App\Http\Livewire\Admin\Blog;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TagForm extends Component
{
    public $tagId = null;
    public $name = '';
    public $slug = '';
    public $slugTouched = false;

    public function mount($tag = null)
    {
        if ($tag) {
            $tag = Tag::findOrFail($tag);

            $this->tagId = $tag->id;
            $this->name = $tag->name;
            $this->slug = $tag->slug;
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
                Rule::unique('blog_tags', 'slug')->ignore($this->tagId),
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

        $tag = Tag::updateOrCreate(
            ['id' => $this->tagId],
            $data
        );

        session()->flash('success', 'Tag salva com sucesso.');

        return redirect()->route('admin.blog.tags.edit', $tag->id);
    }

    public function render()
    {
        return view('livewire.admin.blog.tag-form');
    }
}