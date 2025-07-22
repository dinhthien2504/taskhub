<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission;

class PermissionEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Permission::class;
    }

    public function getAll($with = [], string $search = null, int $perPage = 10)
    {
        $query = $this->_model::with($with);

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function getAllPermissions()
    {
        return $this->_model::select('id', 'name')->get();
    }
}