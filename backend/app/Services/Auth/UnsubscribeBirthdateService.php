<?php

namespace App\Services\Auth;

use App\Repositories\UserEloquentRepository;

class UnsubscribeBirthdateService
{
    protected $userRep;

    public function __construct(UserEloquentRepository $userRep)
    {
        $this->userRep = $userRep;
    }

    public function unsubscribeBirthdayEmail(int $userId)
    {
        $user = $this->userRep->find($userId);
        $user->is_opt_out = true;
        $user->save();
    }
}
