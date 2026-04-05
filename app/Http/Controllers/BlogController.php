<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));
        $tag = trim((string) $request->query('tag', ''));

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $tags = Tag::query()
            ->orderBy('name')
            ->get();

        $posts = Post::query()
            ->with(['author', 'categories', 'tags'])
            ->published()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('title', 'like', '%' . $q . '%')
                        ->orWhere('slug', 'like', '%' . $q . '%')
                        ->orWhere('excerpt', 'like', '%' . $q . '%')
                        ->orWhere('content', 'like', '%' . $q . '%');
                });
            })
            ->when($category !== '', function ($query) use ($category) {
                $query->whereHas('categories', function ($categoryQuery) use ($category) {
                    $categoryQuery->where('slug', $category);
                });
            })
            ->when($tag !== '', function ($query) use ($tag) {
                $query->whereHas('tags', function ($tagQuery) use ($tag) {
                    $tagQuery->where('slug', $tag);
                });
            })
            ->orderByDesc('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('blog.index', compact(
            'posts',
            'q',
            'category',
            'tag',
            'categories',
            'tags'
        ));
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->with(['author', 'categories', 'tags'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = Post::query()
            ->with(['categories', 'tags'])
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->categories->isNotEmpty(), function ($query) use ($post) {
                $categoryIds = $post->categories->pluck('id')->all();

                $query->whereHas('categories', function ($categoryQuery) use ($categoryIds) {
                    $categoryQuery->whereIn('blog_categories.id', $categoryIds);
                });
            })
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }
}