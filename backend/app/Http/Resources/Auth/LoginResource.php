<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'message' => 'Đăng nhập thành công',
            'user_name' => $this['user_name'],
            'avatar' => $this['avatar'],
            'token' => $this['token'],
            'expires_at' => $this['expires_at']->toDateString(),
            'role' => $this['role']
        ];
    }
}
