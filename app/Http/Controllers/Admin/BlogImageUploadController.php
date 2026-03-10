<?php

namespace App\Http\Controllers\Admin;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogImageUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'upload' => ['required', 'image', 'max:4096'],
            'draft_token' => ['nullable', 'string', 'max:255'],
            'post_id' => ['nullable', 'integer', 'exists:blog_posts,id'],
        ]);

        $file = $request->file('upload');

        $directory = 'blog/posts/content';
        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
        $filename = Str::uuid()->toString() . '.' . $extension;

        $path = $file->storeAs($directory, $filename, 'public');
        $url = '/storage/' . ltrim($path, '/');

        Media::create([
            'attachable_type' => $request->filled('post_id') ? Post::class : Post::class,
            'attachable_id' => $request->input('post_id') ?: 0,
            'collection' => 'content',
            'disk' => 'public',
            'directory' => $directory,
            'filename' => $filename,
            'path' => $path,
            'url' => $url,
            'mime_type' => $file->getClientMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
            'draft_token' => $request->input('draft_token'),
        ]);

        return response()->json([
            'url' => $url,
        ]);
    }
}