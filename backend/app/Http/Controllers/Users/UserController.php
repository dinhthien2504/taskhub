<?php

namespace App\Http\Controllers\Users;

use App\Http\Requests\Users\ImportUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\AssignRoleRequest;
use App\Http\Requests\Users\DestroyUserRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Resources\Users\UserRoleAssignmentResource;
use App\Http\Resources\Users\UserDestroyResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Users\UserTrashedResource;
use App\Models\User;
use App\Services\Users\UserService;
use Illuminate\Http\Request;

class UserController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $perPage = $request->query('per_page', 10);

            $users = $this->userService->getAllUsers($search, $perPage);

            return UserResource::collection($users);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy người dùng không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getDropdown()
    {
        try {
            $users = $this->userService->getDropdownQuery();
            return response()->json($users);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy danh sách người dùng không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    public function store(StoreUserRequest $request)
    {
        try {
            $dataUser = $this->userService->createUser($request);
            return new UserResource($dataUser);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Tạo người dùng không thành công.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        return $this->handleUpdate(
            $request,
            $id,
            'Cập nhật người dùng thành công.',
            'Cập nhật người dùng thất bại.'
        );
    }

    public function updateStatus(Request $request, $id)
    {
        return $this->handleUpdate(
            $request,
            $id,
            'Cập nhật trạng thái thành công.',
            'Cập nhật trạng thái thất bại.'
        );
    }

    public function destroy(DestroyUserRequest $request)
    {
        try {
            $ids = $request->input('ids', []);

            $dataDelete = $this->userService->deleteUsers($ids);

            return new UserDestroyResource($dataDelete);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Đã có lỗi xảy ra trong quá trình xóa người dùng.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getUserRoles($id)
    {
        try {
            $result = $this->userService->getUserRoles($id);
            return new UserRoleAssignmentResource($result);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Không thể lấy dữ liệu phân quyền.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function assignRoles(AssignRoleRequest $request, $id)
    {
        try {
            $this->userService->assignRoles($id, $request->roles);
            return response()->json([
                'message' => 'Cập nhật vai trò thành công.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cập nhật vai trò thất bại.',
                'error' => $th->getMessage()
            ]);
        }
    }

    private function handleUpdate(Request $request, $id, string $successMessage, string $errorMessage)
    {
        try {
            $this->userService->updateUser($request, $id);
            return response()->json([
                'message' => $successMessage,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $errorMessage,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getUserTrash()
    {
        try {
            $deletedUsers = $this->userService->getDeletedUsers();
            return UserTrashedResource::collection($deletedUsers)->resolve();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy dữ liệu thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function restoreUser($id)
    {
        try {
            $this->userService->restoreUser($id);

            return response()->json([
                'message' => 'Người dùng đã được khôi phục thành công.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Người dùng khôi phục thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function exportCsv()
    {
        try {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="users.csv"',
            ];
            $callback = $this->userService->exportCsv();
            return response()->stream($callback, 200, $headers);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy dữ liệu thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function importCsv(ImportUserRequest $request)
    {
        try {
            $this->userService->importUsersFromCsv($request->file('file'));

            return response()->json(['message' => 'Đang xử lý file. Vui lòng chờ trong giây lát.']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Thêm dữ liệu thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function downloadCsvTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_template.csv"',
        ];

        $callback = $this->userService->downloadCsvTemplate();

        return response()->stream($callback, 200, $headers);
    }

}