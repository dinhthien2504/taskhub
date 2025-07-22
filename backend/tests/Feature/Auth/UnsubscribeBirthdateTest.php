<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnsubscribeBirthdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_unsubscribe_birthday_email()
    {
        $user = User::factory()->create(['is_opt_out' => false]);

        $response = $this->postJson('api/unsubscribe-birthdate', [
            'user_id' => $user->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Đã hủy đăng ký email sinh nhật thành công.']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_opt_out' => true,
        ]);
    }

    public function test_unsubscribe_fails_with_invalid_user_id()
    {
        $response = $this->postJson('api/unsubscribe-birthdate', [
            'user_id' => 99999,
        ]);

        $response->assertStatus(500);
    }
}
