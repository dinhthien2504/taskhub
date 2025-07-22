<?php

use App\Http\Controllers\EmailTemplates\EmailTemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('email-templates', [EmailTemplateController::class, 'index'])
        ->middleware('permission:view email templates');

    Route::get('email-templates/trashed', [EmailTemplateController::class, 'getTrashedTemplates'])
        ->middleware('permission:view trashed email templates');

    Route::put('email-templates/{id}/restore', [EmailTemplateController::class, 'restoreTemplate'])
        ->middleware('permission:restore email templates');

    Route::post('email-templates', [EmailTemplateController::class, 'store'])
        ->middleware('permission:create email templates');

    Route::put('email-templates/{id}', [EmailTemplateController::class, 'update'])
        ->middleware('permission:update email templates');

    Route::delete('email-templates/{id}', [EmailTemplateController::class, 'destroy'])
        ->middleware('permission:delete email templates');
});