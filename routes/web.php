<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('blog.index');
});

Route::middleware(['auth'])->get('/dashboard', function () {
    return redirect()->route('admin.blog.posts.index');
})->name('dashboard');

require __DIR__ . '/auth.php';
require __DIR__ . '/blog.php';