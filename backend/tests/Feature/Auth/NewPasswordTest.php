<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class NewPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_success()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        // Gửi link reset để lấy token
        $this->postJson('/api/forgot-password', [
            'email' => 'user@example.com',
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;
            return true;
        });

        $response = $this->postJson('/api/reset-password', [
            'email' => 'user@example.com',
            'token' => $token,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => trans(Password::PASSWORD_RESET),
        ]);

        $this->assertTrue(Hash::check('NewPassword1!', $user->fresh()->password));
    }

    public function test_reset_password_with_invalid_token_fails()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'user@example.com',
            'token' => 'invalid-token',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        // $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_reset_password_with_invalid_email_fails()
    {
        $response = $this->postJson('/api/reset-password', [
            'email' => 'notfound@example.com',
            'token' => 'sometoken',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_reset_password_with_invalid_password_confirmation_fails()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $this->postJson('/api/forgot-password', [
            'email' => 'user@example.com',
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;
            return true;
        });

        $response = $this->postJson('/api/reset-password', [
            'email' => 'user@example.com',
            'token' => $token,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'WrongPassword!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }
}