<?php

namespace App\Services\Logs;

use App\Repositories\UserActivityLogEloquentRepository;

class userActivityLogService
{
    protected $userActionLogRepository;

    public function __construct(UserActivityLogEloquentRepository $userActionLogRepository)
    {
        $this->userActionLogRepository = $userActionLogRepository;
    }

    public function getAllLogs(?string $search = null, int $perPage = 10, ?int $month = null, ?int $year = null)
    {
        return $this->userActionLogRepository->getAllLogs(
            with: ['user'],
            search: $search,
            perPage: $perPage,
            month: $month,
            year: $year
        );
    }
}