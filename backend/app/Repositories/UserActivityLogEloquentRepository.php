<?php
namespace App\Repositories;

use App\Models\UserActionLog;
use App\Repositories\EloquentRepository;

class UserActivityLogEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return UserActionLog::class;
    }
    public function getAllLogs(array $with = [], string $search = null, int $perPage = 10, int $month = null, int $year = null)
    {
        $query = $this->_model::with($with);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($month && $year) {
            $query->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);
        } elseif ($year) {
            $query->whereYear('created_at', $year);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
