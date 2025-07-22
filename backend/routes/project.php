<?php

use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Projects\ProjectUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('projects')->group(function () {

        Route::get('/', [ProjectController::class, 'index'])
            ->middleware('can:view any project')
            ->name('projects.index');

        Route::post('/', [ProjectController::class, 'store'])
            ->middleware('can:create project')
            ->name('projects.store');

        Route::put('/{id}', [ProjectController::class, 'update'])
            ->middleware('can:update project')
            ->name('projects.update');

        Route::patch('/{id}', [ProjectController::class, 'updateStatus'])
            ->middleware('can:update project status')
            ->name('projects.updateStatus');

        Route::delete('/{id}', [ProjectController::class, 'destroy'])
            ->middleware('can:delete project')
            ->name('projects.destroy');

        Route::prefix('{project}/users')->group(function () {
            Route::get('/', [ProjectUserController::class, 'index'])
                ->middleware('can:view project users')
                ->name('projects.users.index');

            Route::post('/', [ProjectUserController::class, 'store'])
                ->middleware('can:add project user')
                ->name('projects.users.store');

            Route::delete('/{user}', [ProjectUserController::class, 'destroy'])
                ->middleware('can:remove project user')
                ->name('projects.users.destroy');
        });
    });
});
