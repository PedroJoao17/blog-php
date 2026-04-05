<?php

namespace App\Http\Livewire\Admin\Blog;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\Blog\MediaService;
use App\Services\Blog\PostService;
use Carbon\Carbon;
use Illuminate\Support\Str;
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

    public $category_ids = [];
    public $tag_ids = [];

    public $featuredImageUpload;
    public $currentFeaturedImage = null;

    public $draft_token = '';

    public $showCategoryModal = false;
    public $showTagModal = false;

    public $newCategoryName = '';
    public $newTagName = '';

    public function mount($post = null)
    {
        $this->draft_token = (string) Str::uuid();

        if ($post) {
            $post = Post::findOrFail($post);

            $this->postId = $post->id;
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->excerpt = $post->excerpt ?? '';
            $this->content = $post->content ?? '';
            $this->status = $post->status;
            $this->published_at = $post->published_at
                ? $post->published_at->format('Y-m-d\\TH:i')
                : '';
            $this->category_ids = $post->categories()->pluck('blog_categories.id')->toArray();

            if (empty($this->category_ids) && $post->category_id) {
                $this->category_ids = [(int) $post->category_id];
            }

            $this->tag_ids = $post->tags()->pluck('blog_tags.id')->toArray();
            $this->currentFeaturedImage = $post->featured_image;
        }
    }

    protected function rules()
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
            'featuredImageUpload' => ['nullable', 'image', 'max:2048'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:blog_categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:blog_tags,id'],
        ];

        if ($this->status === 'published') {
            $rules['content'] = ['required', 'string'];
        }

        return $rules;
    }

    protected $messages = [
        'title.required' => 'O título é obrigatório.',
        'content.required' => 'Para publicar, o conteúdo é obrigatório.',
        'featuredImageUpload.image' => 'O arquivo da capa precisa ser uma imagem.',
        'featuredImageUpload.max' => 'A imagem destacada deve ter no máximo 2MB.',
        'category_ids.*.exists' => 'Uma das categorias selecionadas não existe.',
        'tag_ids.*.exists' => 'Uma das tags selecionadas não existe.',
    ];

    public function getIsScheduledProperty(): bool
    {
        if ($this->status !== 'published' || empty($this->published_at)) {
            return false;
        }

        return Carbon::parse($this->published_at)->isFuture();
    }

    public function getIsPubliclyVisibleProperty(): bool
    {
        if ($this->status !== 'published' || empty($this->published_at)) {
            return false;
        }

        return Carbon::parse($this->published_at)->lte(now());
    }

    public function openCategoryModal(): void
    {
        $this->resetValidation('newCategoryName');
        $this->newCategoryName = '';
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal(): void
    {
        $this->resetValidation('newCategoryName');
        $this->newCategoryName = '';
        $this->showCategoryModal = false;
    }

    public function saveNewCategory(): void
    {
        $this->validate([
            'newCategoryName' => ['required', 'string', 'max:255'],
        ], [
            'newCategoryName.required' => 'O nome da categoria é obrigatório.',
        ]);

        $category = Category::create([
            'name' => $this->newCategoryName,
            'slug' => $this->generateUniqueModelSlug(Category::class, $this->newCategoryName),
        ]);

        $this->category_ids = collect($this->category_ids)
            ->push($category->id)
            ->unique()
            ->values()
            ->all();

        $this->closeCategoryModal();
    }

    public function openTagModal(): void
    {
        $this->resetValidation('newTagName');
        $this->newTagName = '';
        $this->showTagModal = true;
    }

    public function closeTagModal(): void
    {
        $this->resetValidation('newTagName');
        $this->newTagName = '';
        $this->showTagModal = false;
    }

    public function saveNewTag(): void
    {
        $this->validate([
            'newTagName' => ['required', 'string', 'max:255'],
        ], [
            'newTagName.required' => 'O nome da tag é obrigatório.',
        ]);

        $tag = Tag::create([
            'name' => $this->newTagName,
            'slug' => $this->generateUniqueModelSlug(Tag::class, $this->newTagName),
        ]);

        $this->tag_ids = collect($this->tag_ids)
            ->push($tag->id)
            ->unique()
            ->values()
            ->all();

        $this->closeTagModal();
    }

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

    public function saveDraft()
    {
        return $this->persist('draft');
    }

    public function publish()
    {
        return $this->persist('published');
    }

    protected function persist(string $status)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $this->status = $status;

        $data = $this->validate();
        $data['status'] = $status;

        $post = app(PostService::class)->saveFromAdminData(
            data: $data,
            postId: $this->postId,
            authorId: auth()->id(),
            draftToken: $this->draft_token,
            featuredImageUpload: $this->featuredImageUpload
        );

        $this->postId = $post->id;
        $this->slug = $post->slug;
        $this->status = $post->status;
        $this->currentFeaturedImage = $post->featured_image;
        $this->category_ids = $post->categories()->pluck('blog_categories.id')->toArray();
        $this->tag_ids = $post->tags()->pluck('blog_tags.id')->toArray();

        session()->flash('success', 'Postagem salva com sucesso.');

        return redirect()->route('admin.blog.posts.edit', $post->id);
    }

    protected function generateUniqueModelSlug(string $modelClass, string $value): string
    {
        $baseSlug = Str::slug($value);

        if ($baseSlug === '') {
            $baseSlug = 'item';
        }

        $slug = $baseSlug;
        $counter = 2;

        while ($modelClass::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function getSelectedCategoriesProperty()
    {
        return Category::query()
            ->whereIn('id', $this->category_ids ?: [])
            ->orderBy('name')
            ->get();
    }

    public function getSelectedTagsProperty()
    {
        return Tag::query()
            ->whereIn('id', $this->tag_ids ?: [])
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.blog.post-form', [
            'categories' => Category::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }
}