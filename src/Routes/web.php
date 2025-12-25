<?php

use atikullahnasar\blog\Http\Controllers\BlogCategoryController;
use atikullahnasar\blog\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('beft')->group(function () {
    Route::resource('blog-categories', BlogCategoryController::class);
    Route::post('blog-categories/{blog_category}/toggle-status', [BlogCategoryController::class, 'toggleStatus'])->name('blog-categories.toggle-status');

    Route::resource('blogs', BlogController::class);
    Route::post('blogs/{blog}', [BlogController::class, 'update'])->name('blogs.update');
    Route::post('blogs/{blog}/toggle-status', [BlogController::class, 'toggleStatus'])->name('blogs.toggle-status');
});