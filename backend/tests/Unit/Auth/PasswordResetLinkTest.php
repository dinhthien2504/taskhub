<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\PasswordResetLinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class PasswordResetLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_reset_link_success()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $service = new PasswordResetLinkService();
        $status = $service->sendResetLink('user@example.com');

        $this->assertEquals(Password::RESET_LINK_SENT, $status);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_send_reset_link_with_nonexistent_email()
    {
        Notification::fake();

        $service = new PasswordResetLinkService();
        $status = $service->sendResetLink('notfound@example.com');

        $this->assertEquals(Password::INVALID_USER, $status);

        Notification::assertNothingSent();
    }
}