<?php

namespace App\Repositories;

use App\Models\TaskStatus;

class TaskStatusEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return TaskStatus::class;
    }
}