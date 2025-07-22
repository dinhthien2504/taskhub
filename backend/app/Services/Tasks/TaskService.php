<?php

namespace App\Services\Tasks;

use App\Models\Project;
use App\Repositories\TaskEloquentRepository;
use App\Repositories\TaskStatusLogEloquentRepository;

class TaskService
{
    protected $taskRepository;
    protected $taskStatusLogRepository;

    public function __construct(
        TaskEloquentRepository $taskRepository,
        TaskStatusLogEloquentRepository $taskStatusLogRepository,
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskStatusLogRepository = $taskStatusLogRepository;
    }

    public function getTasks(Project $project, $request)
    {
        return $this->taskRepository->getTasksByProject($project, $request->search);
    }

    public function getTask(Project $project, $id)
    {
        return $this->taskRepository->getTaskById($project, $id);
    }

    public function createTask($request, Project $project)
    {
        $data = [
            'task_status_id' => $request->task_status_id,
            'title' => $request->title,
            'created_by' => auth()->id(),
        ];

        if ($request->has('description')) {
            $data['description'] = $request->description;
        }

        if ($request->has('assigned_to')) {
            $data['assigned_to'] = $request->assigned_to;
        }

        if ($request->has('start_date')) {
            $data['start_date'] = $request->start_date;
        }

        if ($request->has('due_date')) {
            $data['due_date'] = $request->due_date;
        }
        $task = $project->tasks()->create($data);
        //Tạo log
        $this->taskStatusLogRepository->create([
            'task_id' => $task->id,
            'old_status_id' => null,
            'new_status_id' => $request->task_status_id,
            'user_id' => auth()->id(),
            'start_time' => now(),
            'end_time' => null,
        ]);
        $task->load(['assignee']);
        return $task;
    }

    public function updateTask($request, Project $project, $id)
    {
        $task = $this->taskRepository->findByProject($project, $id);

        if (!$task) {
            throw new \Exception('Task not found');
        }

        $originalStatus = $task->task_status_id;

        $updatableFields = [
            'title',
            'description',
            'assigned_to',
            'task_status_id',
            'start_date',
            'due_date',
        ];

        $data = [];

        foreach ($updatableFields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->$field;
            }
        }

        $task->update($data);

        // Kiểm tra nếu trạng thái có thay đổi → tạo log
        if (isset($data['task_status_id']) && $data['task_status_id'] != $originalStatus) {
            $this->taskStatusLogRepository->updateCurrentLogEndTime($task->id);

            $this->taskStatusLogRepository->create([
                'task_id' => $task->id,
                'old_status_id' => $originalStatus,
                'new_status_id' => $data['task_status_id'],
                'user_id' => auth()->id(),
                'start_time' => now(),
                'end_time' => null,
            ]);
        }

        $task->load(['assignee']);
        return $task;
    }


}