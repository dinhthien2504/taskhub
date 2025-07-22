<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordChangeRequest;
use App\Services\Auth\PasswordChangeService;

class PasswordChangeController extends Controller
{
    protected $passwordChangeService;

    public function __construct(PasswordChangeService $passwordChangeService)
    {
        $this->passwordChangeService = $passwordChangeService;
    }

    public function update(PasswordChangeRequest $request)
    {
        $user = $request->user();

        $result = $this->passwordChangeService->change(
            $user,
            $request->current_password,
            $request->password
        );

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return response()->json([
            'message' => 'Đổi mật khẩu thành công, vui lòng đăng nhập lại.',
        ], 200);
    }
}
