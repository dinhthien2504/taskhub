<?php

namespace App\Http\Resources\Logs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserActionLogResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user->name ?? null,
            'email' => $this->user->email ?? null,
            'description' => $this->description,
            'action' => $this->action,
            'created_at' => $this->created_at
        ];
    }
}
