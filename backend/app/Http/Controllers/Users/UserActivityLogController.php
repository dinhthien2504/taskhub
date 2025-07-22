<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logs\UserActionLogRequest;
use App\Http\Resources\Logs\UserActionLogResource;
use App\Services\Logs\userActivityLogService;

class UserActivityLogController extends Controller
{
    protected $userActivityLogService;

    public function __construct(userActivityLogService $userActivityLogService)
    {
        $this->userActivityLogService = $userActivityLogService;
    }
    public function index(UserActionLogRequest $request)
    {
        $UserActivityLogs = $this->userActivityLogService->getAllLogs(
            search: $request->input('search'),
            perPage: $request->input('per_page', 10),
            month: $request->input('month'),
            year: $request->input('year')
        );
        return UserActionLogResource::collection($UserActivityLogs);
    }
}