<?php

namespace App\Http\Controllers\RolePermissions;

use App\Http\Requests\RolePermissions\StoreRoleRequest;
use App\Http\Requests\RolePermissions\UpdateRolePermissionRequest;
use App\Http\Requests\RolePermissions\UpdateRoleRequest;
use App\Http\Requests\RolePermissions\DestroyRoleRequest;
use App\Http\Resources\RolePermissions\PermissionAssignmentResource;
use App\Http\Resources\RolePermissions\RoleDestroyResource;
use App\Http\Resources\RolePermissions\RoleResource;
use App\Services\RolePermissions\RoleService;
use Illuminate\Http\Request;

class RoleController
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $perPage = $request->query('per_page', 10);

            $roles = $this->roleService->getAllRoles($search, $perPage);

            return RoleResource::collection($roles)->response();

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy vai trò không thành công.'
            ], 500);
        }
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            $dataRole = $this->roleService->createRole($request);

            return (new RoleResource($dataRole))
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Tạo vai trò không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $this->roleService->updateRole($request, $id);

            return response()->json([
                'message' => 'Cập nhật vai trò thành công.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cập nhật vai trò không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(DestroyRoleRequest $request)
    {
        try {
            $ids = $request->input('ids', []);

            $dataDelete = $this->roleService->deleteRoles($ids);

            return new RoleDestroyResource($dataDelete);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Đã có lỗi xảy ra trong quá trình xóa vai trò.',
            ], 500);
        }
    }

    public function getPermissionAssignment($role_id)
    {
        try {
            $result = $this->roleService->getPermissionAssignment($role_id);
            return new PermissionAssignmentResource($result);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Không thể lấy dữ liệu phân quyền.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updatePermissionAssignment($id, UpdateRolePermissionRequest $request)
    {
        try {
            $permissionIds = $request->input('permission_ids', []);
            $this->roleService->updatePermissionAssignment($id, $permissionIds);

            return response()->json([
                'message' => 'Cập nhật phân quyền thành công.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cập nhật phân quyền thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}