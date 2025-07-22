<?php

namespace App\Services\Auth;

use App\Repositories\UserEloquentRepository;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    protected $userRepository;

    public function __construct(UserEloquentRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getProfile($userId)
    {
        $user = $this->userRepository->find($userId);
        return $user;
    }

    public function updateProfile($userId, array $data, $avatar = null)
    {
        $user = $this->userRepository->find($userId);
        if ($avatar) {
            if($user && $user->avatar) {
                $oldPath = public_path('images/users/'.$user->avatar);
                if(file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            
            $imageName = time() . '_' . $avatar->getClientOriginalName();
            $avatar->move(public_path('images/users'), $imageName);
            $data['avatar'] = $imageName;
        }
        return $this->userRepository->update($userId, $data);
    }
}