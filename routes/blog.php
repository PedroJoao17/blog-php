<?php

use App\Http\Controllers\BlogController;
use App\Http\Livewire\Admin\Blog\CategoryForm;
use App\Http\Livewire\Admin\Blog\CategoryIndex;
use App\Http\Livewire\Admin\Blog\PostForm;
use App\Http\Livewire\Admin\Blog\PostIndex;
use App\Http\Livewire\Admin\Blog\TagForm;
use App\Http\Livewire\Admin\Blog\TagIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

Route::middleware(['web', 'auth'])
    ->prefix('admin/blog')
    ->name('admin.blog.')
    ->group(function () {
        Route::get('/posts', PostIndex::class)->name('posts.index');
        Route::get('/posts/create', PostForm::class)->name('posts.create');
        Route::get('/posts/{post}/edit', PostForm::class)->name('posts.edit');

        Route::get('/categories', CategoryIndex::class)->name('categories.index');
        Route::get('/categories/create', CategoryForm::class)->name('categories.create');
        Route::get('/categories/{category}/edit', CategoryForm::class)->name('categories.edit');

        Route::get('/tags', TagIndex::class)->name('tags.index');
        Route::get('/tags/create', TagForm::class)->name('tags.create');
        Route::get('/tags/{tag}/edit', TagForm::class)->name('tags.edit');
    });