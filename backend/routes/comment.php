<?php

use App\Http\Controllers\Projects\CommentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('tasks/{task}')->group(function () {
    Route::post('comments', [CommentController::class, 'store'])
        ->middleware('can:create comment')
        ->name('tasks.comments.store');
});
