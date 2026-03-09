<?php

namespace App\Http\Livewire\Admin\Blog;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PostForm extends Component
{
    public $postId = null;
    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $status = 'draft';
    public $published_at = '';
    public $category_id = '';
    public $slugTouched = false;

    public function mount($post = null)
    {
        if ($post) {
            $post = Post::findOrFail($post);

            $this->postId = $post->id;
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->excerpt = $post->excerpt ?? '';
            $this->content = $post->content ?? '';
            $this->status = $post->status;
            $this->published_at = $post->published_at
                ? $post->published_at->format('Y-m-d\TH:i')
                : '';
            $this->category_id = $post->category_id ?? '';
            $this->slugTouched = true;
        }
    }

    public function updatedTitle($value)
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
        $this->slug = Str::slug($this->title);
        $this->slugTouched = true;
    }

    protected function rules()
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_posts', 'slug')->ignore($this->postId),
            ],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'category_id' => ['nullable', 'exists:blog_categories,id'],
        ];

        if ($this->status === 'published') {
            $rules['content'] = ['required', 'string'];
        }

        return $rules;
    }

    protected $messages = [
        'title.required' => 'O título é obrigatório.',
        'slug.required' => 'O slug é obrigatório.',
        'slug.unique' => 'Este slug já está em uso.',
        'content.required' => 'Para publicar, o conteúdo é obrigatório.',
    ];

    public function save()
    {
        $data = $this->validate();

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($data['status'] === 'draft') {
            $data['published_at'] = $data['published_at'] ?: null;
        }

        $post = Post::updateOrCreate(
            ['id' => $this->postId],
            [
                'author_id' => auth()->id(),
                'category_id' => $data['category_id'] ?: null,
                'title' => $data['title'],
                'slug' => $data['slug'],
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'status' => $data['status'],
                'published_at' => $data['published_at'],
            ]
        );

        session()->flash('success', 'Postagem salva com sucesso.');

        return redirect()->route('admin.blog.posts.edit', $post->id);
    }

    public function render()
    {
        return view('livewire.admin.blog.post-form', [
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }
}