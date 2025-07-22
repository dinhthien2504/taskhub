<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\NewPasswordService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class NewPasswordServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_success()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        // Gửi link reset để lấy token
        Password::sendResetLink(['email' => 'user@example.com']);
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;
            return true;
        });

        $service = new NewPasswordService();

        $request = new \Illuminate\Http\Request([
            'email' => 'user@example.com',
            'token' => $token,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $status = $service->reset($request);

        $this->assertEquals(Password::PASSWORD_RESET, $status);
        $this->assertTrue(Hash::check('NewPassword1!', $user->fresh()->password));
    }

    public function test_reset_password_with_invalid_token_fails()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('OldPassword1!'),
        ]);

        $service = new NewPasswordService();

        $request = new \Illuminate\Http\Request([
            'email' => 'user@example.com',
            'token' => 'invalid-token',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $status = $service->reset($request);

        $this->assertEquals(Password::INVALID_TOKEN, $status);
        // Kiểm tra mật khẩu cũ vẫn còn
        $this->assertTrue(Hash::check('OldPassword1!', $user->fresh()->password));
    }

    public function test_reset_password_with_invalid_email_fails()
    {
        $service = new NewPasswordService();

        $request = new \Illuminate\Http\Request([
            'email' => 'notfound@example.com',
            'token' => 'sometoken',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $status = $service->reset($request);

        $this->assertEquals(Password::INVALID_USER, $status);
    }
}