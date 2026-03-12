<?php

namespace App\Services\Blog;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function storeFeaturedImage(Post $post, UploadedFile $file, ?int $uploadedBy = null): Media
    {
        $this->deleteFeaturedImage($post);

        $directory = 'blog/posts/featured';
        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
        $filename = Str::uuid()->toString() . '.' . $extension;

        $path = $file->storeAs($directory, $filename, 'public');
        $url = '/storage/' . ltrim($path, '/');

        $media = Media::create([
            'attachable_type' => Post::class,
            'attachable_id' => $post->id,
            'collection' => 'featured',
            'disk' => 'public',
            'directory' => $directory,
            'filename' => $filename,
            'path' => $path,
            'url' => $url,
            'mime_type' => $file->getClientMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'uploaded_by' => $uploadedBy,
            'draft_token' => null,
        ]);

        $post->update([
            'featured_image' => $url,
        ]);

        return $media;
    }

    public function deleteFeaturedImage(Post $post): void
    {
        $oldFeatured = $post->media()
            ->where('collection', 'featured')
            ->latest('id')
            ->first();

        if ($oldFeatured) {
            $this->deleteMediaRecordAndFile($oldFeatured);
        }

        if ($post->featured_image) {
            $post->update([
                'featured_image' => null,
            ]);
        }
    }

    public function deleteMediaRecordAndFile(Media $media): void
    {
        if ($media->disk && $media->path && Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }

        $media->delete();
    }

    public function attachDraftContentMediaToPost(Post $post, string $draftToken, int $uploadedBy): void
    {
        Media::query()
            ->where('collection', 'content')
            ->where('attachable_type', Post::class)
            ->where('attachable_id', 0)
            ->where('draft_token', $draftToken)
            ->where('uploaded_by', $uploadedBy)
            ->update([
                'attachable_id' => $post->id,
                'draft_token' => null,
                'updated_at' => now(),
            ]);
    }

    public function syncContentMediaWithHtml(Post $post, ?string $html): void
    {
        $referencedUrls = $this->extractManagedContentUrlsFromHtml($html);

        $contentMedia = $post->media()
            ->where('collection', 'content')
            ->get();

        foreach ($contentMedia as $media) {
            if (!in_array($media->url, $referencedUrls, true)) {
                $this->deleteMediaRecordAndFile($media);
            }
        }
    }

    public function cleanupAbandonedDraftMedia(int $hours = 24): int
    {
        $deleted = 0;

        $staleMedia = Media::query()
            ->where('collection', 'content')
            ->where('attachable_type', Post::class)
            ->where('attachable_id', 0)
            ->whereNotNull('draft_token')
            ->where('created_at', '<', now()->subHours($hours))
            ->get();

        foreach ($staleMedia as $media) {
            $this->deleteMediaRecordAndFile($media);
            $deleted++;
        }

        return $deleted;
    }

    public function extractManagedContentUrlsFromHtml(?string $html): array
    {
        if (!is_string($html) || trim($html) === '') {
            return [];
        }

        $matches = [];

        preg_match_all(
            '#(?:https?://[^"\']+)?(/storage/blog/posts/content/[^"\'>\s]+)#i',
            $html,
            $matches
        );

        return collect($matches[1] ?? [])
            ->map(function ($path) {
                return '/' . ltrim($path, '/');
            })
            ->unique()
            ->values()
            ->all();
    }

    public function storeContentImageUpload(
        \Illuminate\Http\UploadedFile $file,
        ?int $uploadedBy = null,
        ?string $draftToken = null,
        ?int $postId = null
    ): Media {
        $directory = 'blog/posts/content';
        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
        $filename = \Illuminate\Support\Str::uuid()->toString() . '.' . $extension;

        $path = $file->storeAs($directory, $filename, 'public');
        $url = '/storage/' . ltrim($path, '/');

        return Media::create([
            'attachable_type' => \App\Models\Post::class,
            'attachable_id' => $postId ?: 0,
            'collection' => 'content',
            'disk' => 'public',
            'directory' => $directory,
            'filename' => $filename,
            'path' => $path,
            'url' => $url,
            'mime_type' => $file->getClientMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'uploaded_by' => $uploadedBy,
            'draft_token' => $draftToken,
        ]);
    }
}