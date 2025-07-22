<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Repositories\UserEloquentRepository;
use App\Services\Auth\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_profile_with_avatar()
    {
        $user = User::factory()->create(['avatar' => null]);

        $userRepository = new UserEloquentRepository();

        $service = new ProfileService($userRepository);

        $data = [
            'name' => 'Test Name',
            'phone' => '0987654321',
        ];
        $avatar = UploadedFile::fake()->image('avatar.png', 100, 100)->size(500);

        $service->updateProfile($user->id, $data, $avatar);

        $user->refresh();
        $this->assertEquals('Test Name', $user->name);
        $this->assertEquals('0987654321', $user->phone);
        $this->assertNotNull($user->avatar);
        $this->assertFileExists(public_path('images/users/' . $user->avatar));
    }

    public function test_update_profile_without_avatar()
    {
        $user = User::factory()->create(['avatar' => null]);

        $userRepository= new UserEloquentRepository();
        $service = new ProfileService($userRepository);

        $data = [
            'name' => 'No Avatar',
            'phone' => '0123456789',
        ];

        $service->updateProfile($user->id, $data, null);

        $user->refresh();
        $this->assertEquals('No Avatar', $user->name);
        $this->assertEquals('0123456789', $user->phone);
    }
}