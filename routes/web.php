<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('blog.index');
});

require __DIR__ . '/blog.php';