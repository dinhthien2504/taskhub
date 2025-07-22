<?php
namespace App\Services\Auth;

use App\Repositories\UserEloquentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    protected $userRepository;

    public function __construct(UserEloquentRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register($request)
    {
        return DB::transaction(function () use ($request) {
            $user = $this->userRepository->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->sendEmailVerificationNotification();

            $token = $user->createToken('auth_token')->plainTextToken;

            $expires_at = now()->addDays(1);
            $user->tokens()->latest()->update([
                'expires_at' => $expires_at
            ]);

            return [
                'user_name' => $user->name,
                'token' => $token,
                'expires_at' => $expires_at
            ];
        });
    }
}