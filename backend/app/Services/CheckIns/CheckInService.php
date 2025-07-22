<?php

namespace App\Services\CheckIns;

use App\Repositories\CheckInEloquentRepository;

class CheckInService
{
    protected $checkIn_rep;

    public function __construct(CheckInEloquentRepository $checkIn_rep)
    {
        $this->checkIn_rep = $checkIn_rep;
    }

    public function getCheckInLogs($request)
    {
        $filters = [];

        if ($request->filled('username')) {
            $filters['username'] = $request->input('username');
        }

        if ($request->filled('date')) {
            $filters['date'] = $request->input('date');
        }

        if ($request->filled('status')) {
            $filters['status'] = $request->input('status');
        }

        if ($request->filled('per_page') && is_numeric($request->input('per_page'))) {
            $filters['per_page'] = (int) $request->input('per_page');
        } else {
            $filters['per_page'] = 15;
        }

        return $this->checkIn_rep->getFilteredLogs($filters);
    }

    public function getTodayLog()
    {
        $userId = auth()->id();
        $log = $this->checkIn_rep->getTodayLog($userId);
        return $log;
    }

    public function checkIn()
    {
        $userId = auth()->id();
        $log = $this->checkIn_rep->getTodayLog($userId);

        if ($log && $log->check_in_time) {
            abort(500, 'Bạn đã checkin hôm nay rồi.');
        }

        $log = $this->checkIn_rep->createCheckIn($userId);
        return $log;
    }

    public function checkOut()
    {
        $userId = $userId = auth()->id();
        $log = $this->checkIn_rep->getTodayLog($userId);

        if (!$log || !$log->check_in_time) {
            abort(500, 'Bạn phải checkin trước khi checkout.');
        }

        if ($log->check_out_time) {
            abort(500, 'Bạn đã checkout hôm nay rồi.');
        }

        $log = $this->checkIn_rep->updateCheckOut($log);
        return $log;
    }

    public function exportCsv($request)
    {
        $logs = $this->getCheckInLogs($request);

        return function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['ID', 'Tên', 'Email', 'Ngày Check-in', 'Ngày Check-out', 'Đi trễ']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->user->name ?? '',
                    $log->user->email ?? '',
                    $log->check_in_time,
                    $log->check_out_time ?? 'Chưa check-out',
                    $log->is_late ? 'Đã đi trễ ' . $log->late_by_minutes . ' phút' : 'Đúng giờ',
                ]);
            }

            fclose($handle);
        };
    }
}
