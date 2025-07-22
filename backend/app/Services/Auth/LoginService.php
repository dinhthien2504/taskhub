<?php
namespace App\Services\Auth;

class LoginService
{
    public function login($user, $request)
    {
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        $expires_at = 1;
        if ($request->boolean('remember')) {
            $expires_at = 7;
        }
        $expires_at = now()->addDays($expires_at);

        $user->tokens()->latest()->update([
            'expires_at' => $expires_at
        ]);

        return [
            'user_name' => $user->name,
            'token' => $token,
            'expires_at' => $expires_at
        ];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
    }
}