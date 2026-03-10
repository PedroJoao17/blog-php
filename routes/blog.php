<?php

use App\Http\Controllers\BlogController;
use App\Http\Livewire\Admin\Blog\PostForm;
use App\Http\Livewire\Admin\Blog\PostIndex;
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
    });