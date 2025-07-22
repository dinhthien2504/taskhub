<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Password;

class PasswordResetLinkService
{
    public function sendResetLink($email)
    {
        return Password::sendResetLink(['email' => $email]);
    }
}