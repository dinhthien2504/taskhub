<?php

namespace App\Services\RolePermissions;

use App\Repositories\PermissionEloquentRepository;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    protected $permissionRepository;

    public function __construct(PermissionEloquentRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function getAllPermissions(?string $search = null, int $perPage = 10)
    {
        return $this->permissionRepository->getAll(['roles'], $search, $perPage);
    }

    public function createPermission($request)
    {
        $permissionData = [
            'name' => $request->name,
            'guard_name' => 'web'
        ];
        if (isset($request->description)) {
            $permissionData['description'] = $request->description;
        }
        return DB::transaction(function () use ($permissionData) {
            return $this->permissionRepository->create($permissionData);
        });
    }

    public function updatePermission($request, $id)
    {
        $permissionData = [
            'name' => $request->name
        ];
        if (isset($request->description)) {
            $permissionData['description'] = $request->description;
        }
        return DB::transaction(function () use ($permissionData, $id) {
            return $this->permissionRepository->update($id, $permissionData);
        });
    }

    public function deletePermissions(array $ids): array
    {
        $permissions = $this->permissionRepository->getByIds($ids);

        $cannotDelete = [];
        $canDelete = [];

        foreach ($permissions as $permission) {
            if ($permission->roles()->exists()) {
                $cannotDelete[] = $permission->name;
                continue;
            }

            $canDelete[] = $permission->id;
        }

        if (!empty($canDelete)) {

            DB::transaction(function () use ($canDelete) {
                $this->permissionRepository->deleteByIds($canDelete);
            });
        }

        return [
            'deleted_ids' => $canDelete,
            'not_deleted_names' => implode(', ', $cannotDelete)
        ];
    }
}