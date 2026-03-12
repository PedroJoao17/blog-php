<?php

namespace App\Http\Controllers\Admin;

use App\Services\Blog\MediaService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BlogImageUploadController extends Controller
{
    public function store(Request $request, MediaService $mediaService)
    {
        $request->validate([
            'upload' => ['required', 'image', 'max:4096'],
            'draft_token' => ['nullable', 'string', 'max:255'],
            'post_id' => ['nullable', 'integer', 'exists:blog_posts,id'],
        ]);

        $media = $mediaService->storeContentImageUpload(
            file: $request->file('upload'),
            uploadedBy: auth()->id(),
            draftToken: $request->input('draft_token'),
            postId: $request->input('post_id')
        );

        return response()->json([
            'url' => $media->url,
        ]);
    }
}