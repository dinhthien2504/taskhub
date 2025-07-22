<?php

namespace App\Http\Resources\RolePermissions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionAssignmentResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'all_permissions' => $this['all_permissions'],
            'role_permissions' => $this['role_permissions']
        ];
    }
}