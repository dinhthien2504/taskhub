<?php

namespace App\Services\Auth;

class EmailVerificationService
{
    public function sendVerification($user)
    {
        if ($user->hasVerifiedEmail()) {
            return [
                'status' => 'verified',
                'message' => 'Email đã được xác nhận'
            ];
        }

        $user->sendEmailVerificationNotification();

        return [
            'status' => 'sent',
            'message' => 'Đã gửi link xác minh'
        ];
    }
}