<?php

use App\Http\Controllers\Projects\ProjectUserController;
use App\Http\Controllers\Users\UserActivityLogController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\UserImportExportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('permission:view users');

    Route::get('/users/dropdown', [UserController::class, 'getDropdown'])
        ->name('users.getDropdown');

    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('permission:create users');

    Route::put('/users/{id}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('permission:edit users');

    Route::patch('/users/{id}/status', [UserController::class, 'updateStatus'])
        ->name('users.updateStatus')
        ->middleware('permission:edit users');

    Route::delete('/users', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('permission:delete users');

    Route::get('/users/{id}/roles', [UserController::class, 'getUserRoles'])
        ->name('users.roles')
        ->middleware('permission:view user roles');

    Route::put('/users/{id}/roles', [UserController::class, 'assignRoles'])
        ->name('users.assign-roles')
        ->middleware('permission:assign roles to users');

    Route::get('/users/trashed', [UserController::class, 'getUserTrash'])
        ->name('users.trashed')
        ->middleware('permission:view users');

    Route::put('/users/{id}/restore', [UserController::class, 'restoreUser'])
        ->name('users.restore')
        ->middleware('permission:restore users');

    Route::get('/users/projects', [ProjectUserController::class, 'getProjectByUser'])
        ->name('users.projects.getProjectByUser')
        ->middleware('permission:view project user');

    Route::get('/users/activity-logs', [UserActivityLogController::class, 'index'])
        ->name('users.activity-logs')
        ->middleware('permission:view user activity logs');

    Route::get('users/export-csv', [UserController::class, 'exportCsv'])
        ->name('users.export-csv')
        ->middleware('permission:export users csv');

    Route::get('users/csv-template', [UserController::class, 'downloadCsvTemplate'])
        ->name('users.csv-template')
        ->middleware('permission:download users csv template');

    Route::post('users/import-csv', [UserController::class, 'importCsv'])
        ->name('users.import-csv')
        ->middleware('permission:import users from csv');

});