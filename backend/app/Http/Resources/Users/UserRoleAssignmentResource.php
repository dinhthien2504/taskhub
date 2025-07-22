<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRoleAssignmentResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'assigned_roles' => $this['assigned_roles'],
            'all_roles' => $this['all_roles']
        ];
    }
}