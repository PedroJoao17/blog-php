<?php

namespace App\Services\Blog;

use App\Models\Post;
use Illuminate\Http\UploadedFile;

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
        $data = $this->normalizeData($data);

        $post = Post::updateOrCreate(
            ['id' => $postId],
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

        $post->tags()->sync($data['tag_ids'] ?? []);

        $this->mediaService->attachDraftContentMediaToPost($post, $draftToken, $authorId);
        $this->mediaService->syncContentMediaWithHtml($post, $data['content']);

        if ($featuredImageUpload) {
            $this->mediaService->storeFeaturedImage($post, $featuredImageUpload, $authorId);
        }

        return $post->fresh(['author', 'category', 'tags', 'media']);
    }

    public function delete(Post $post): void
    {
        foreach ($post->media as $media) {
            $this->mediaService->deleteMediaRecordAndFile($media);
        }

        $post->delete();
    }

    protected function normalizeData(array $data): array
    {
        $data['content'] = $this->htmlContentSanitizer->sanitize($data['content'] ?? '');

        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (($data['status'] ?? 'draft') === 'draft') {
            $data['published_at'] = $data['published_at'] ?: null;
        }

        return $data;
    }
}