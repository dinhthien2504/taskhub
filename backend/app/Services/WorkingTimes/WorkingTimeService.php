<?php

namespace App\Services\WorkingTimes;

use App\Repositories\WorkingTimeEloquentRepository;

class WorkingTimeService
{
    protected $workingTimeRepo;

    public function __construct(WorkingTimeEloquentRepository $workingTimeRepo)
    {
        $this->workingTimeRepo = $workingTimeRepo;
    }

    public function getWorkingTime() 
    {
        return $this->workingTimeRepo->getWorkingTime();
    }

    public function upsert($request)
    {
        $data = [];
        if ($request->filled('start_time')) {
            $data['start_time'] = $request->input('start_time');
        }
        if ($request->filled('late_after')) {
            $data['late_after'] = $request->input('late_after');
        }
        if ($request->filled('end_time')) {
            $data['end_time'] = $request->input('end_time');
        }
        return $this->workingTimeRepo->upsert($data);
    }
}