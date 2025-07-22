<?php

use App\Http\Controllers\Campaigns\CampaignUserController;
use Illuminate\Support\Facades\Route;


Route::prefix('campaigns/{campaign}')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        Route::get('/users', [CampaignUserController::class, 'index'])
            ->name('campaign.users.index')
            ->middleware('permission:view campaign users');

        Route::post('/users', [CampaignUserController::class, 'assign'])
            ->name('campaign.users.assign')
            ->middleware('permission:assign campaign users');

        Route::delete('/users/{user}', [CampaignUserController::class, 'remove'])
            ->name('campaign.users.remove')
            ->middleware('permission:remove campaign users');
    });
