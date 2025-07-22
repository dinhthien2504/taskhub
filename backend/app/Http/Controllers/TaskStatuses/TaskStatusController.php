<?php

namespace App\Http\Controllers\TaskStatuses;

use App\Http\Resources\TaskStatuses\TaskStatusResource;
use App\Services\TaskStatuses\TaskStatusService;


class TaskStatusController
{
    protected $taskStatusService;

    public function __construct(TaskStatusService $taskStatusService)
    {
        $this->taskStatusService = $taskStatusService;
    }

    public function index()
    {
        try {
            $task_statuses = $this->taskStatusService->getAllStatuses();
            return TaskStatusResource::collection($task_statuses)->resolve();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy trạng thái không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}