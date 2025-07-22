<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\LoginService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_service_creates_token_and_sets_expiry()
    {
        $user = User::factory()->create([
            'name' => 'Nguyễn Văn A',
            'email' => 'unit@example.com',
            'password' => bcrypt('@Password1!'),
        ]);

        $service = new LoginService();

        // Test không remember
        $request = new \Illuminate\Http\Request([
            'remember' => false,
        ]);
        $result = $service->login($user, $request);

        $this->assertEquals('Nguyễn Văn A', $result['user_name']);
        $this->assertNotEmpty($result['token']);

        $token = $user->tokens()->latest()->first();
        $this->assertNotNull($token);
        $this->assertEquals(
            now()->addDays(1)->format('Y-m-d'),
            $token->expires_at->format('Y-m-d')
        );

        // Test có remember
        $request = new \Illuminate\Http\Request([
            'remember' => true,
        ]);
        $result = $service->login($user, $request);

        $token = $user->tokens()->latest()->first();
        $this->assertEquals(
            now()->addDays(7)->format('Y-m-d'),
            $token->expires_at->format('Y-m-d')
        );
    }
}