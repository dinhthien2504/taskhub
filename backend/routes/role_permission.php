<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolePermissions\RoleController;
use App\Http\Controllers\RolePermissions\PermissionController;
Route::middleware('auth:sanctum')->group(function () {
    /*
        ROLES
    */
    Route::get('/roles', [RoleController::class, 'index'])
        ->name('roles.index')
        ->middleware('permission:view roles');

    Route::post('/roles', [RoleController::class, 'store'])
        ->name('roles.store')
        ->middleware('permission:create roles');

    Route::put('/roles/{id}', [RoleController::class, 'update'])
        ->name('roles.update')
        ->middleware('permission:edit roles');

    Route::delete('/roles', [RoleController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('permission:delete roles');

    Route::prefix('roles')->group(function () {
        Route::get('{role}/permissions', [RoleController::class, 'getPermissionAssignment'])
            ->name('roles.get-permission-assignment')
            ->middleware('permission:view role permissions');

        Route::put('{role}/permissions', [RoleController::class, 'updatePermissionAssignment'])
            ->name('roles.update-permission-assignment')
            ->middleware('permission:edit role permissions');
    });

    /*
        PERMISSIONS
    */
    Route::get('/permissions', [PermissionController::class, 'index'])
        ->name('permissions.index')
        ->middleware('permission:view permissions');

    Route::post('/permissions', [PermissionController::class, 'store'])
        ->name('permissions.store')
        ->middleware('permission:create permissions');

    Route::put('/permissions/{id}', [PermissionController::class, 'update'])
        ->name('permissions.update')
        ->middleware('permission:edit permissions');

    Route::delete('/permissions', [PermissionController::class, 'destroy'])
        ->name('permissions.destroy')
        ->middleware('permission:delete permissions');
});
