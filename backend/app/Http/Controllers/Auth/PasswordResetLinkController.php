<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetLinkRequest;
use App\Services\Auth\PasswordResetLinkService;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    protected $passwordResetLinkService;

    public function __construct(PasswordResetLinkService $passwordResetLinkService)
    {
        $this->passwordResetLinkService = $passwordResetLinkService;
    }

    public function store(PasswordResetLinkRequest $request)
    {
        $status = $this->passwordResetLinkService->sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => __($status),
                'errors' => [
                    'email' => ['Không tìm thấy người dùng với địa chỉ email này.'],
                ]

            ], 422);
        }

        return response()->json(['status' => __($status)]);
    }
}
