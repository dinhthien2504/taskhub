<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'active' => $this->active ?? 1,
            'roles' => $this->roles->pluck('name'),
            'created_at' => optional($this->created_at)->toDateString(),
            'email_verified_at' => optional($this->email_verified_at)->toDateString(),
        ];
    }
}
