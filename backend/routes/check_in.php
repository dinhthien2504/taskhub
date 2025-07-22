<?php

use App\Http\Controllers\CheckIns\CheckInController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('check-in-logs', [CheckInController::class, 'index'])
        ->middleware('permission:view check in logs');
    Route::get('check-in-logs/export', [CheckInController::class, 'exportCSV'])
        ->middleware('permission:view check in logs');
    Route::get('today-log', [CheckInController::class, 'getTodayLog']);
    Route::post('check-in', [CheckInController::class, 'checkIn']);
    Route::post('check-out', [CheckInController::class, 'checkOut']);
});