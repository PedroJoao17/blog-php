<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->with(['author', 'category'])
            ->published()
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->with(['author', 'category', 'tags'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('blog.show', compact('post'));
    }
}