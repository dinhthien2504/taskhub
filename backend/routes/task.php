<?php

use App\Http\Controllers\Projects\TaskController;
use Illuminate\Support\Facades\Route;
Route::middleware('auth:sanctum')->prefix('projects/{project}')->group(function () {

    Route::get('tasks', [TaskController::class, 'index'])
        ->middleware('can:view any task')
        ->name('projects.tasks.index');

    Route::post('tasks', [TaskController::class, 'store'])
        ->middleware('can:create task')
        ->name('projects.tasks.store');

    Route::get('tasks/{id}', [TaskController::class, 'show'])
        ->middleware('can:view task')
        ->name('projects.tasks.show');

    Route::put('tasks/{id}', [TaskController::class, 'update'])
        ->middleware('can:update task')
        ->name('projects.tasks.update');

    Route::delete('tasks/{id}', [TaskController::class, 'destroy'])
        ->middleware('can:delete task')
        ->name('projects.tasks.destroy');
});