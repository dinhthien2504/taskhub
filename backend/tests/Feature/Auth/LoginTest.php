<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_succeeds_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('@Password1'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => '@Password1',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'user_name',
            'token',
            'expires_at',
        ]);

        $this->assertEquals('Đăng nhập thành công', $response->json('message'));
        $this->assertEquals($user->name, $response->json('user_name'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('@Correct-password1'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_login_with_remember_sets_7_day_expiry()
    {
        $user = User::factory()->create([
            'email' => 'remember@example.com',
            'password' => Hash::make('@Password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'remember@example.com',
            'password' => '@Password123',
            'remember' => true,
        ]);

        $response->assertStatus(200);
        $expiresAt = now()->addDays(7)->toDateString();
        $this->assertEquals($expiresAt, $response->json('expires_at'));
    }

    public function test_login_fails_when_required_fields_missing()
    {
        $response = $this->postJson('api/login', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_fails_when_invalid_email_format()
    {
        $response = $this->postJson('api/login', [
            'not-an-email',
            '@Passowrd1'
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email']);
    }
    public function test_logout()
    {
        //Tạo user
        $user = User::factory()->create();
        $token = $user->createToken('Auth_Test')->plainTextToken;

        $accessToken = PersonalAccessToken::findToken(explode('|', $token)[1]);
        $accessToken->expires_at = Carbon::now()->addMinutes(10);
        $accessToken->save();

        //Gọi api logout

        $res = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/logout');

        //Kiểm tra trạng thái 
        $res->assertStatus(200);

        //Kiểm tra token đã xóa chưa
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $accessToken->id
        ]);
    }
}
