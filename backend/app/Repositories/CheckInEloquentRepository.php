<?php

namespace App\Repositories;

use App\Models\CheckInLog;
use App\Models\WorkingTime;
use Illuminate\Database\Eloquent\Builder;


class CheckInEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return CheckInLog::class;
    }

    public function getFilteredLogs(array $filters)
    {
        $query = $this->_model::query()->with('user');

        if (!empty($filters['username'])) {
            $query->whereHas('user', function (Builder $q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['username'] . '%');
            });
        }

        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'checked_in':
                    $query->whereNotNull('check_in_time')->whereNull('check_out_time');
                    break;
                case 'checked_out':
                    $query->whereNotNull('check_in_time')->whereNotNull('check_out_time');
                    break;
            }
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->orderByDesc('date')->paginate($perPage);
    }


    public function getTodayLog($userId)
    {
        return $this->_model::where('user_id', $userId)
            ->whereDate('date', today())
            ->first();
    }

    public function createCheckIn($userId)
    {
        $checkInTime = now();

        $workingTime = WorkingTime::first();

        $lateLimit = $workingTime && $workingTime->late_after
            ? now()->copy()->setTimeFromTimeString($workingTime->late_after)
            : now()->copy()->setTime(8, 15, 0);

        $isLate = $checkInTime->gt($lateLimit);
        $lateBy = $isLate ? $lateLimit->diffInMinutes($checkInTime, false) : null;

        return $this->_model::create([
            'user_id' => $userId,
            'date' => today(),
            'check_in_time' => $checkInTime,
            'is_late' => $isLate,
            'late_by_minutes' => $lateBy,
            'status' => $isLate ? 'Đi trễ' : 'Đúng giờ',
        ]);
    }

    public function updateCheckOut(CheckInLog $log)
    {
        $log->check_out_time = now();
        $log->save();

        return $log;
    }
}
