<?php

use App\Http\Controllers\TaskStatuses\TaskStatusController;
use Illuminate\Support\Facades\Route;

Route::get('task_statuses', [TaskStatusController::class, 'index']);