<?php

namespace App\Http\Livewire\Admin\Blog;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\Blog\MediaService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class PostForm extends Component
{
    use WithFileUploads;

    public $postId = null;
    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $status = 'draft';
    public $published_at = '';
    public $category_id = '';
    public $slugTouched = false;

    public $featuredImageUpload;
    public $currentFeaturedImage = null;

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
            $this->currentFeaturedImage = $post->featured_image;
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
            'featuredImageUpload' => ['nullable', 'image', 'max:2048'],
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
        'featuredImageUpload.image' => 'O arquivo da capa precisa ser uma imagem.',
        'featuredImageUpload.max' => 'A imagem destacada deve ter no máximo 2MB.',
    ];

    public function removeFeaturedImage(MediaService $mediaService)
    {
        if (!$this->postId) {
            $this->featuredImageUpload = null;
            $this->currentFeaturedImage = null;
            return;
        }

        $post = Post::findOrFail($this->postId);
        $mediaService->deleteFeaturedImage($post);

        $this->featuredImageUpload = null;
        $this->currentFeaturedImage = null;

        session()->flash('success', 'Imagem destacada removida com sucesso.');
    }

    public function save(MediaService $mediaService)
    {
        $data = $this->validate();

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($data['status'] === 'draft') {
            $data['published_at'] = $data['published_at'] ?: null;
        }

        $authorId = auth()->id() ?: User::query()->value('id');

        $post = Post::updateOrCreate(
            ['id' => $this->postId],
            [
                'author_id' => $authorId,
                'category_id' => $data['category_id'] ?: null,
                'title' => $data['title'],
                'slug' => $data['slug'],
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'status' => $data['status'],
                'published_at' => $data['published_at'],
            ]
        );

        if ($this->featuredImageUpload) {
            $mediaService->storeFeaturedImage($post, $this->featuredImageUpload, $authorId);
            $this->currentFeaturedImage = $post->fresh()->featured_image;
        }

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
