<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Http\Resources\Tasks\TaskResource;
use App\Http\Resources\Tasks\TaskShowResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\Tasks\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request, Project $project)
    {
        try {
            $tasksByProject = $this->taskService->getTasks($project, $request);
            return TaskResource::collection($tasksByProject)->resolve();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy danh sách task thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    public function store(StoreTaskRequest $request, Project $project)
    {
        try {
            $task = $this->taskService->createTask($request, $project);
            return new TaskResource($task);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Thêm task thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show(Project $project, $id)
    {
        try {
            $task = $this->taskService->getTask($project, $id);
            return new TaskShowResource($task);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy task thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(UpdateTaskRequest $request, Project $project, $id)
    {
        try {
            $task = $this->taskService->updateTask($request, $project, $id);
            return new TaskResource($task);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cập nhật task thất bại.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }


    public function destroy(Project $project, Task $task)
    {
        $task->delete();
        return response()->noContent();
    }
}