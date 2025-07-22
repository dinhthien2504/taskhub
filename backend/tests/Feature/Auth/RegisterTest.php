<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\VerifyEmailCustom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_fails_with_invalid_email()
    {
        Notification::fake();
        $response = $this->postJson('api/register', [
            'name' => 'Nguyễn Văn A',
            'email' => 'invalid-email',
            'password' => '@Password1!',
            'password_confirmation' => '@Password1!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

    }

    public function test_register_fails_if_email_exists()
    {
        Notification::fake();
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('api/register', [
            'name' => 'Nguyễn Văn A',
            'email' => 'test@example.com',
            'password' => '@Password1!',
            'password_confirmation' => '@Password1!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

    }

    public function test_register_succeeds_with_valid_data()
    {
        Notification::fake();
        $response = $this->postJson('api/register', [
            'name' => 'Nguyễn Văn A',
            'email' => 'new@example.com',
            'password' => '@Password1!',
            'password_confirmation' => '@Password1!',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Tạo tài khoản thành công',
            'user_name' => 'Nguyễn Văn A',
        ]);

        $user = User::where('email', 'new@example.com')->first();
        Notification::assertSentTo($user, VerifyEmailCustom::class);


        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_register_fails_when_required_fields_missing()
    {
        $response = $this->postJson('api/register', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_register_fails_when_password_confirmation_does_not_match()
    {
        $response = $this->postJson('api/register', [
            'name' => 'Ngô Đình Thiên',
            'email' => 'dinhthien2504@gmail.com',
            'password' => '@Password1',
            'password_confirmation' => 'diferent_password'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_register_fails_when_password_too_short()
    {
        $response = $this->postJson('api/register', [
            'name' => 'Nguyễn Văn A',
            'email' => 'shortpass@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_register_fails_when_email_has_spaces()
    {
        $response = $this->postJson('api/register', [
            'name' => 'Nguyễn Văn A',
            'email' => 'abc @gmail.com',
            'password' => '@Password1!',
            'password_confirmation' => '@Password1!',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_notification_not_sent_when_registration_fails()
    {
        Notification::fake();
        $response = $this->postJson('api/register', [
            'name' => '',
            'email' => 'fail@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ]);
        $response->assertStatus(422);
        Notification::assertNothingSent();
    }

    public function test_register_with_uppercase_email()
    {
        $response = $this->postJson('api/register', [
            'name' => 'Nguyễn Văn A',
            'email' => 'UPPERCASE@EXAMPLE.COM',
            'password' => '@Password1!',
            'password_confirmation' => '@Password1!',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'UPPERCASE@EXAMPLE.COM']);
    }
}
