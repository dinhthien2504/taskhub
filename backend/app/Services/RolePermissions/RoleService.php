<?php

namespace App\Services\RolePermissions;

use App\Repositories\PermissionEloquentRepository;
use App\Repositories\RoleEloquentRepository;
use Illuminate\Support\Facades\DB;

class RoleService
{
    protected $roleRepository;
    protected $permissionRepository;

    public function __construct(
        RoleEloquentRepository $roleRepository,
        PermissionEloquentRepository $permissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function getAllRoles(?string $search = null, int $perPage = 10)
    {
        return $this->roleRepository->getAll(['permissions', 'users'], $search, $perPage);
    }

    public function createRole($request)
    {
        return DB::transaction(function () use ($request) {
            $roleData = [
                'name' => $request->name,
                'guard_name' => 'web'
            ];
            if (isset($request->description)) {
                $roleData['description'] = $request->description;
            }
            return $this->roleRepository->create($roleData);
        });
    }

    public function updateRole($request, $id)
    {
        $roleData = [
            'name' => $request->name
        ];
        if (isset($request->description)) {
            $roleData['description'] = $request->description;
        }
        return DB::transaction(function () use ($roleData, $id) {
            $role = $this->roleRepository->find($id);

            if (!$role) {
                throw new \Exception("Vai trò không tồn tại với ID: $id");
            }
            return $this->roleRepository->update($id, $roleData);
        });
    }

    public function deleteRoles(array $ids): array
    {
        $roles = $this->roleRepository->getByIds($ids);

        $cannotDelete = [];
        $canDelete = [];

        foreach ($roles as $role) {
            if ($role->users()->exists()) {
                $cannotDelete[] = $role->name;
                continue;
            }

            $canDelete[] = $role->id;
        }

        if (!empty($canDelete)) {

            DB::transaction(function () use ($canDelete) {
                $this->roleRepository->deleteByIds($canDelete);
            });
        }

        return [
            'deleted_ids' => $canDelete,
            'not_deleted_names' => implode(', ', $cannotDelete)
        ];
    }

    public function getPermissionAssignment(int $role_id): array
    {
        $allPermissions = $this->permissionRepository->getAllPermissions();

        $role = $this->roleRepository->findWithPermissions($role_id);

        if (!$role) {
            throw new \Exception("Vai trò không tồn tại.");
        }
        $rolePermissionIds = $role->permissions->pluck('id');

        return [
            'all_permissions' => $allPermissions,
            'role_permissions' => $rolePermissionIds,
        ];
    }

    public function updatePermissionAssignment(int $roleId, array $permissionIds)
    {
        $role = $this->roleRepository->find($roleId);
        return $role->syncPermissions($permissionIds);
    }
}