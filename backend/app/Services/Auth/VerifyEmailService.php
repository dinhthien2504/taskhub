<?php

namespace App\Services\Auth;

use Illuminate\Auth\Events\Verified;
use App\Models\User;

class VerifyEmailService
{
    public function verify($id, $hash, $request)
    {
        if (!$request->hasValidSignature()) {
            return [
                'status' => false,
                'code' => 403,
                'message' => 'Verification link không hợp lệ hoặc đã hết hạn'
            ];
        }

        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return [
                'status' => false,
                'code' => 403,
                'message' => 'Invalid verification link'
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'status' => true,
                'code' => 200,
                'message' => 'Email đã được xác thực'
            ];
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return [
            'status' => true,
            'code' => 201,
            'message' => 'Xác thực email thành công'
        ];
    }
}