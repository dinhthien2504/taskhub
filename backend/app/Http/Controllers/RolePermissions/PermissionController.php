<?php

namespace App\Http\Controllers\RolePermissions;

use App\Http\Requests\RolePermissions\DestroyPermissionRequest;
use App\Http\Requests\RolePermissions\StorePermissionRequest;
use App\Http\Requests\RolePermissions\UpdatePermissionRequest;
use App\Http\Resources\RolePermissions\PermissionDestroyResource;
use App\Http\Resources\RolePermissions\PermissionResource;
use App\Services\RolePermissions\PermissionService;
use Illuminate\Http\Request;

class PermissionController
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $perPage = $request->query('per_page', 10);

            $permissions = $this->permissionService->getAllPermissions($search, $perPage);

            return PermissionResource::collection($permissions)->response();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy danh sách quyềnkhông thành công.',
                'error' => $th->getMessage()
            ],  500);
        }

    }

    public function store(StorePermissionRequest $request)
    {
        try {
            $dataPermission = $this->permissionService->createPermission($request);

            return (new PermissionResource($dataPermission))
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Tạo quyền không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        try {
            $this->permissionService->updatePermission($request, $id);

            return response()->json([
                'message' => 'Cập nhật quyền thành công.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cập nhật quyền không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(DestroyPermissionRequest $request)
    {
        try {
            $ids = $request->input('ids', []);

            $dataDelete = $this->permissionService->deletePermissions($ids);

            return new PermissionDestroyResource($dataDelete);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Đã có lỗi xảy ra trong quá trình xóa quyền.',
            ], 500);
        }
    }
}