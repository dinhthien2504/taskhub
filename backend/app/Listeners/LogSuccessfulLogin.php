<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Helpers\ActivityLogger;
class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        ActivityLogger::log('login', 'User logged in.');
    }
}
