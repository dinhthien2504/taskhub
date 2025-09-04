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
        $user->load('roles');
        return [
            'user_name' => $user->name,
            'avatar' => $user->avatar,
            'token' => $token,
            'expires_at' => $expires_at,
            'role' => $user->roles?->pluck('name')->first()
        ];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
    }
}