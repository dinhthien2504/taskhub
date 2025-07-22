<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Repositories\UserEloquentRepository;
use App\Services\Auth\PasswordChangeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_change_password_success()
    {
        $user = User::factory()->create([
            'password' => bcrypt('OldPassword1!'),
        ]);

        $userRepository = new UserEloquentRepository();

        $service = new PasswordChangeService($userRepository);

        $result = $service->change($user, 'OldPassword1!', 'NewPassword1!');

        $this->assertTrue($result);
        $this->assertTrue(Hash::check('NewPassword1!', $user->fresh()->password));
    }

    public function test_change_password_fails_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('OldPassword1!'),
        ]);

        $userRepository = new UserEloquentRepository();

        $service = new PasswordChangeService($userRepository);

        $result = $service->change($user, 'WrongPassword!', 'NewPassword1!');

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $response = $result->getData(true);
        $this->assertEquals('The given data was invalid.', $response['message']);
        $this->assertArrayHasKey('current_password', $response['errors']);
        $this->assertEquals(['Mật khẩu hiện tại không đúng.'], $response['errors']['current_password']);
        // Đảm bảo mật khẩu không bị đổi
        $this->assertTrue(Hash::check('OldPassword1!', $user->fresh()->password));
    }
}