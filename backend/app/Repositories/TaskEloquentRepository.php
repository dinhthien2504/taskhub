<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Task;

class TaskEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Task::class;
    }

    public function getTasksByProject(Project $project, ?string $search = null)
    {
        $query = $project->tasks()->with('assignee');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    public function findByProject(Project $project, $taskId)
    {
        return $project->tasks()->where('id', $taskId)->first();
    }

    public function getTaskById(Project $project, $taskId)
    {
        $task = $project->tasks()
            ->with([
                'assignee',
                'project',
                'logs' => function ($query) {
                    $query->with(['user', 'oldStatus', 'newStatus'])
                        ->orderBy('created_at', 'desc');
                },
                'comments' => function ($query) {
                    $query->with('user')->orderBy('created_at', 'desc');
                }
            ])
            ->where('id', $taskId)
            ->firstOrFail();

        return $task;
    }


}