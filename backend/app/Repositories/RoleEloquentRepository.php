<?php

namespace App\Repositories;

use App\Models\Role;

class RoleEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Role::class;
    }

    public function getAll($with = [], string $search = null, int $perPage = 10)
    {
        $query = $this->_model::with($with);

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }
        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function findWithPermissions(int $id)
    {
        return $this->_model::with('permissions')->find($id);
    }

}