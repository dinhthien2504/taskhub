<?php

use App\Http\Controllers\Campaigns\CampaignController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('campaigns', [CampaignController::class, 'index'])
        ->middleware('permission:view campaigns');

    Route::get('campaigns/trashed', [CampaignController::class, 'getCampaignsTrash'])
        ->middleware('permission:view trashed campaigns');

    Route::put('campaigns/{id}/restore', [CampaignController::class, 'restoreCampaign'])
        ->middleware('permission:restore campaigns');

    Route::post('campaigns', [CampaignController::class, 'store'])
        ->middleware('permission:create campaigns');

    Route::put('campaigns/{id}', [CampaignController::class, 'update'])
        ->middleware('permission:update campaigns');

    Route::delete('campaigns/{id}', [CampaignController::class, 'destroy'])
        ->middleware('permission:delete campaigns');

    Route::post('/campaigns/{id}/send', [CampaignController::class, 'send'])
        ->middleware('permission:send campaigns');
});