<?php

use App\Http\Controllers\WorkingTimes\WorkingTimeController;
use Illuminate\Support\Facades\Route;

Route::get('/working-time', [WorkingTimeController::class, 'index'])
    ->middleware(['auth:sanctum', 'permission:view working time']);

Route::put('/working-time', [WorkingTimeController::class, 'upsert'])
    ->middleware(['auth:sanctum', 'permission:edit working time']);