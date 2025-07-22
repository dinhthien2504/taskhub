<?php
namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Repositories\EloquentRepository;

class UserEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return User::class;
    }

    public function getAll($with = [], string $search = null, int $perPage = 10)
    {
        $query = $this->_model::with($with)
            ->where('id', '!=', auth()->id());

        $currentUser = auth()->user();

        $myRoles = $currentUser->roles()->withCount('permissions')->get();

        $myPermissionCount = $myRoles->max('permissions_count') ?? 0;

        //Lọc ra nhưng roles có vai trò cao hơn người dùng hiện tại
        $higherRoleNames = Role::withCount('permissions')
            ->get()
            ->filter(fn($role) => $role->permissions_count > $myPermissionCount)
            ->pluck('name')
            ->toArray();
        //Loại bỏ người dùng có vai trò cao hơn
        $query->whereDoesntHave('roles', function ($q) use ($higherRoleNames) {
            $q->whereIn('name', $higherRoleNames);
        });

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function getAllRoles()
    {
        return Role::pluck('name');
    }

    public function getDeletedUsers()
    {
        return $this->_model::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
    }

    public function findWithTrashed($id)
    {
        return $this->_model::withTrashed()->find($id);
    }

    public function getDropdownQuery()
    {
        return $this->_model::query()
            ->orderBy('name');
    }

}
