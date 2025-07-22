<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Repositories\UserEloquentRepository;
use App\Services\Auth\RegisterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_service_creates_user_and_token_and_sends_verification()
    {
        Notification::fake();

        $userRepository = new UserEloquentRepository();

        $service = new RegisterService($userRepository);

        $request = new \Illuminate\Http\Request([
            'name' => 'Nguyễn Văn A',
            'email' => 'unit@example.com',
            'password' => '@Password1!',
        ]);

        $result = $service->register($request);

        // Kiểm tra trả về đúng dữ liệu
        $this->assertEquals('Nguyễn Văn A', $result['user_name']);
        $this->assertNotEmpty($result['token']);
        $this->assertNotEmpty($result['expires_at']);

        // Kiểm tra user đã được tạo
        $user = User::where('email', 'unit@example.com')->first();
        $this->assertNotNull($user);

        // Kiểm tra đã gửi email xác thực
        Notification::assertSentTo($user, \App\Notifications\VerifyEmailCustom::class);

        // Kiểm tra token đã được lưu với expires_at đúng
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'expires_at' => $result['expires_at'],
        ]);
    }
}