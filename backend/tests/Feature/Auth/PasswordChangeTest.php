<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_change_password_with_expired_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('@Oldpassword1'),
        ]);

        $token = $user->createToken('TestToken')->plainTextToken;

        $accessToken = PersonalAccessToken::findToken(explode('|', $token)[1]);
        $accessToken->expires_at = Carbon::now()->subMinutes(1);
        $accessToken->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/change-password', [
                    'current_password' => '@Oldpassword1',
                    'password' => '@Newpassword456',
                    'password_confirmation' => '@Newpassword456',
                ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Token đã hết hạn, vui lòng đăng nhập lại.',
            ]);
    }

    public function test_password_can_be_updated_successfully()
    {
        $user = User::factory()->create([
            'password' => bcrypt('@Oldpasswrod1'),
        ]);


        $token = $user->createToken('TestToken')->plainTextToken;

        $accessToken = PersonalAccessToken::findToken(explode('|', $token)[1]);
        $accessToken->expires_at = Carbon::now()->addMinutes(10);
        $accessToken->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/change-password', [
                    'current_password' => '@Oldpasswrod1',
                    'password' => '@Newpassword456',
                    'password_confirmation' => '@Newpassword456',
                ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Đổi mật khẩu thành công, vui lòng đăng nhập lại.',
            ]);

        $this->assertTrue(Hash::check('@Newpassword456', $user->fresh()->password));

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_password_update_fails_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('@Correctpassword1'),
        ]);

        $token = $user->createToken('TestToken')->plainTextToken;

        $accessToken = PersonalAccessToken::findToken(explode('|', $token)[1]);
        $accessToken->expires_at = Carbon::now()->addMinutes(10);
        $accessToken->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/change-password', [
                    'current_password' => '@Wrongpassword1',
                    'password' => '@Newpassword456',
                    'password_confirmation' => '@Newpassword456',
                ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['current_password']);
        $response->assertJsonFragment([
            'current_password' => ['Mật khẩu hiện tại không đúng.'],
        ]);

        $this->assertTrue(Hash::check('@Correctpassword1', $user->fresh()->password));
    }

    public function test_password_update_fails_with_invalid_data()
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $accessToken = PersonalAccessToken::findToken(explode('|', $token)[1]);
        $accessToken->expires_at = Carbon::now()->addMinutes(10);
        $accessToken->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('api/change-password', [
                    'current_password' => '@Oldpassword1',
                    'password' => 'short',
                    'password_confirmation' => '',
                ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_password_update_fails_when_required_fields_missing()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        $accessToken = PersonalAccessToken::findToken(explode('|', $token)[1]);
        $accessToken->expires_at = now()->addMinutes(10);
        $accessToken->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/change-password', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['current_password', 'password']);
    }

    public function test_password_update_fails_when_password_confirmation_does_not_match()
    {
        $user = User::factory()->create([
            'password' => bcrypt('@Oldpassword1'),
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;
        $accessToken = PersonalAccessToken::findToken(explode('|', $token)[1]);
        $accessToken->expires_at = now()->addMinutes(10);
        $accessToken->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/change-password', [
                    'current_password' => '@Oldpassword1',
                    'password' => '@Newpassword456',
                    'password_confirmation' => 'notmatch',
                ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_password_update_fails_when_not_authenticated()
    {
        $response = $this->putJson('/api/change-password', [
            'current_password' => '@Oldpassword1',
            'password' => '@Newpassword456',
            'password_confirmation' => '@Newpassword456',
        ]);
        $response->assertStatus(status: 401);
    }
}
