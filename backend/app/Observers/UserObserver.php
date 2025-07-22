<?php

namespace App\Observers;

use App\Models\User;
use App\Helpers\ActivityLogger;

class UserObserver
{
    public function created(User $user): void
    {
        ActivityLogger::log('create', "User #$user->id created.");
    }

    public function updated(User $user): void
    {
        ActivityLogger::log('update', "User #$user->id updated.");
    }

    public function deleted(User $user): void
    {
        ActivityLogger::log('delete', "User #$user->id deleted.");
    }

    public function restored(User $user): void
    {
        ActivityLogger::log('restore', "User #$user->id restored.");
    }

    public function forceDeleted(User $user): void
    {
        ActivityLogger::log('force_delete', "User #$user->id permanently deleted.");
    }
}