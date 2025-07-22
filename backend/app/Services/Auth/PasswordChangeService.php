<?php

namespace App\Services\Auth;

use App\Repositories\UserEloquentRepository;
use Illuminate\Support\Facades\Hash;

class PasswordChangeService
{
    protected $userRepository;

    public function __construct(UserEloquentRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function change($user, $current_password, $password)
    {
        if (!Hash::check($current_password, $user->password)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'current_password' => ['Mật khẩu hiện tại không đúng.']
                ]
            ], 422);
        }

        $this->userRepository->update($user->id, [
            'password' => bcrypt($password),
        ]);

        $user->tokens()->delete();

        return true;
    }
}