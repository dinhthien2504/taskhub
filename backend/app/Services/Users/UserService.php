<?php

namespace App\Services\Users;

use App\Jobs\ImportUsersJob;
use App\Models\User;
use App\Repositories\UserEloquentRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserEloquentRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(?string $search = null, int $perPage = 10)
    {

        return $this->userRepository->getAll([], $search, $perPage);
    }

    public function getDropdownQuery()
    {
        return $this->userRepository->getDropdownQuery()
            ->get(['id', 'name']);
    }


    public function createUser($request)
    {
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];
        return DB::transaction(function () use ($userData) {
            $user = $this->userRepository->create($userData);
            $user->sendEmailVerificationNotification();
            return $user;
        });
    }

    public function updateUser($request, $id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new \Exception('Người dùng không tồn tại.');
        }
        $userData = [];

        if ($request->name) {
            $userData['name'] = $request->name;
        }

        if (!is_null($request->active)) {
            $userData['active'] = $request->active;
        }

        return $this->userRepository->update($id, $userData);
    }

    public function deleteUsers(array $ids): array
    {
        $users = $this->userRepository->getByIds($ids);
        $cannotDelete = [];
        $canDelete = [];

        foreach ($users as $user) {
            if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
                $cannotDelete[] = $user->name;
                continue;
            }
            $canDelete[] = $user->id;
        }

        if (!empty($canDelete)) {
            DB::transaction(function () use ($canDelete) {
                $this->userRepository->deleteByIds($canDelete);
            });
        }

        return [
            'deleted_ids' => $canDelete,
            'not_deleted_names' => implode(', ', $cannotDelete)
        ];
    }

    public function getUserRoles($user_id)
    {
        $user = $this->userRepository->find($user_id);
        return [
            'assigned_roles' => $user->roles->pluck('name'),
            'all_roles' => $this->userRepository->getAllRoles()
        ];
    }

    public function assignRoles($id, array $roles): void
    {
        $user = $this->userRepository->find($id);
        $user->syncRoles($roles);
    }

    public function getDeletedUsers()
    {
        return $this->userRepository->getDeletedUsers();
    }

    public function restoreUser($id): array
    {
        $user = $this->userRepository->findWithTrashed($id);
        $user->restore();
        return [
            'user' => $user
        ];
    }
    public function exportCsv()
    {
        $users = User::all();
        return function () use ($users) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['id', 'name', 'email', 'created_at']);
            foreach ($users as $user) {
                fputcsv($handle, [$user->id, $user->name, $user->email, $user->created_at]);
            }
            fclose($handle);
        };
    }

    public function downloadCsvTemplate()
    {
        return function () {
            // Ghi BOM đầu dòng để Excel nhận đúng UTF-8
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['name', 'email', 'phone']);

            fclose($handle);
        };
    }

    public function importUsersFromCsv(UploadedFile $file): void
    {
        $user = auth()->user();
        Storage::makeDirectory('imports');

        $path = $file->storeAs('imports', uniqid('users_') . '.csv');
        
        ImportUsersJob::dispatch($path, $user->email);
    }

}