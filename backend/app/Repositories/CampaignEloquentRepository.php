<?php

namespace App\Repositories;

use App\Models\Campaign;


class CampaignEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Campaign::class;
    }

    public function getAllCampaigns(array $with = [], string $search = null, int $perPage = 10, int $month = null, int $year = null, string $status = null)
    {
        $query = $this->_model::with($with);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($month && $year) {
            $query->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);
        } elseif ($year) {
            $query->whereYear('created_at', $year);
        }

        if ($status) {
            $query->where('status', $status);
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getDeletedCampaigns()
    {
        return $this->_model::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
    }

}