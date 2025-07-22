<?php

namespace App\Services\TaskStatuses;

use App\Repositories\TaskStatusEloquentRepository;
class TaskStatusService
{
    protected $taskStatusRepository;

    public function __construct(TaskStatusEloquentRepository $taskStatusRepository)
    {
        $this->taskStatusRepository = $taskStatusRepository;
    }

    public function getAllStatuses(): mixed
    {
        return $this->taskStatusRepository->getAll();
    }
}