<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\ProfileResource;
use App\Services\Auth\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(Request $request)
    {
        try {
            $user = $request->user();
            $profile = $this->profileService->getProfile(userId: $user->id);

            return new ProfileResource($profile);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy thông tin không thành công.'
            ]);
        }
    }

    public function update(ProfileRequest $request)
    {
        try {
            $user = $request->user();
            $data = $request->only(['name', 'email', 'phone']);
            $avatar = $request->file('avatar');

            $result = $this->profileService->updateProfile($user->id, $data, $avatar);
            return (new ProfileResource($result));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật tài khoản của bạn.'
            ], 500);
        }
    }
}