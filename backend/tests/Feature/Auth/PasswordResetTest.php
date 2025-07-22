<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_password_reset_link_succeeds()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => trans('passwords.sent'),
        ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }
    
    public function test_send_password_reset_link_with_nonexistent_email(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'notfound@example.com',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email']);
        // $response->assertJsonFragment([
        //     'message' => ['Không tìm thấy người dùng với địa chỉ email này.'],
        // ]);

        Notification::assertNothingSent();
    }

    public function test_send_password_reset_link_fails_with_invalid_email()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_send_password_reset_link_fails_when_email_missing()
    {
        $response = $this->postJson('api/forgot-password', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email']);
    }
}
