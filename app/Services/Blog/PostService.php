<?php

namespace App\Services\Blog;

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(
        protected HtmlContentSanitizer $htmlContentSanitizer,
        protected MediaService $mediaService
    ) {
    }

    public function saveFromAdminData(
        array $data,
        ?int $postId,
        int $authorId,
        string $draftToken,
        ?UploadedFile $featuredImageUpload = null
    ): Post {
        $data = $this->normalizeData($data, $postId);

        $primaryCategoryId = $data['category_ids'][0] ?? null;

        $post = Post::updateOrCreate(
            ['id' => $postId],
            [
                'author_id' => $authorId,
                'category_id' => $primaryCategoryId,
                'title' => $data['title'],
                'slug' => $data['slug'],
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'status' => $data['status'],
                'published_at' => $data['published_at'],
            ]
        );

        $post->categories()->sync($data['category_ids'] ?? []);
        $post->tags()->sync($data['tag_ids'] ?? []);

        $this->mediaService->attachDraftContentMediaToPost($post, $draftToken, $authorId);
        $this->mediaService->syncContentMediaWithHtml($post, $data['content']);

        if ($featuredImageUpload) {
            $this->mediaService->storeFeaturedImage($post, $featuredImageUpload, $authorId);
        }

        return $post->fresh(['author', 'category', 'categories', 'tags', 'media']);
    }

    public function delete(Post $post): void
    {
        foreach ($post->media as $media) {
            $this->mediaService->deleteMediaRecordAndFile($media);
        }

        $post->delete();
    }

    protected function normalizeData(array $data, ?int $postId = null): array
    {
        $data['title'] = trim((string) ($data['title'] ?? ''));
        $data['excerpt'] = trim((string) ($data['excerpt'] ?? ''));
        $data['content'] = $this->htmlContentSanitizer->sanitize($data['content'] ?? '');
        $data['category_ids'] = collect($data['category_ids'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $data['tag_ids'] = collect($data['tag_ids'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $data['slug'] = $this->generateUniqueSlug($data['title'], $postId);

        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (($data['status'] ?? 'draft') === 'draft') {
            $data['published_at'] = $data['published_at'] ?: null;
        }

        return $data;
    }

    protected function generateUniqueSlug(string $title, ?int $ignorePostId = null): string
    {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'post';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (
            Post::query()
                ->when($ignorePostId, fn($query) => $query->where('id', '!=', $ignorePostId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}