<?php

use Illuminate\Support\Facades\Route;
// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
//     Route::get('/test', function () {
//         return response()->json([
//             'message' => 'Đây là nội dung test'
//         ]);
//     });
// });
require __DIR__ . '/auth.php';
require __DIR__ . '/role_permission.php';
require __DIR__ . '/user.php';
require __DIR__ . '/project.php';
require __DIR__ . '/task_status.php';
require __DIR__ . '/task.php';
require __DIR__ . '/comment.php';
require __DIR__ . '/campaign.php';
require __DIR__ . '/campaign_user.php';
require __DIR__ . '/email_template.php';
require __DIR__ . '/check_in.php';
require __DIR__ . '/working_time.php';