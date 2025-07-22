<?php
namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'message' => 'Tạo tài khoản thành công',
            'user_name' => $this['user_name'],
            'token' => $this['token'],
            'expires_at' => $this['expires_at']->toDateString(),
        ];
    }
}