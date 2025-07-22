<?php

namespace App\Repositories;

use App\Models\TaskStatusLog;

class TaskStatusLogEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return TaskStatusLog::class;
    }

    public function updateCurrentLogEndTime($taskId)
    {
        return $this->_model
            ->where('task_id', $taskId)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first()?->update(['end_time' => now()]);
    }

}