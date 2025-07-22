<?php

namespace App\Http\Resources\RolePermissions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'description' => $this->description,
            'permissions_count' => $this->permissions->count(),
            'users_count' => $this->users->count(),
            'created_at' => $this->created_at->toDateString()
        ];
    }
}
