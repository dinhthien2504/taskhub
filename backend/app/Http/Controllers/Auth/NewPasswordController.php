<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewPasswordRequest;
use App\Services\Auth\NewPasswordService;
use Illuminate\Support\Facades\Password;

class NewPasswordController extends Controller
{
    protected $newPasswordService;

    public function __construct(NewPasswordService $newPasswordService)
    {
        $this->newPasswordService = $newPasswordService;
    }

    public function store(NewPasswordRequest $request)
    {
        $status = $this->newPasswordService->reset($request);

        if ($status != Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => [__($status)],
                ]
            ], 422);
        }

        return response()->json(['status' => __($status)], 200);
    }
}