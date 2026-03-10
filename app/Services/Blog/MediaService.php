<?php

namespace App\Services\Blog;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
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
            if ($oldFeatured->disk && $oldFeatured->path && Storage::disk($oldFeatured->disk)->exists($oldFeatured->path)) {
                Storage::disk($oldFeatured->disk)->delete($oldFeatured->path);
            }

            $oldFeatured->delete();
        }

        if ($post->featured_image) {
            $post->update([
                'featured_image' => null,
            ]);
        }
    }
}