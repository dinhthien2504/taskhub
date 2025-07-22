<?php

namespace App\Http\Controllers\WorkingTimes;

use App\Http\Requests\WorkingTimes\StoreOrUpdateWorkingTimeRequest;
use App\Services\WorkingTimes\WorkingTimeService;

class WorkingTimeController
{
    protected $workingTimeService;

    public function __construct(WorkingTimeService $workingTimeService)
    {
        $this->workingTimeService = $workingTimeService;
    }

    public function index() 
    {
        try {
            $response = $this->workingTimeService->getWorkingTime();
            return response()->json($response);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy thời gian làm việc thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function upsert(StoreOrUpdateWorkingTimeRequest $request)
    {
        try {
            $response = $this->workingTimeService->upsert($request);

            return response()->json($response, 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy người dùng không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}