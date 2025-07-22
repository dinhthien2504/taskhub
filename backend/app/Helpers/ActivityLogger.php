<?php

namespace App\Helpers;

use App\Models\UserActionLog;

class ActivityLogger
{
    public static function log(string $action, string $description): void
    {
        UserActionLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }
}