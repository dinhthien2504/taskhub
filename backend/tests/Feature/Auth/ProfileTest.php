<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_with_avatar()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100)->size(500);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST', '/api/profile', [
                '_method' => 'PUT',
                'name' => 'Nguyen Van Test',
                'phone' => '0123456789',
                'avatar' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Cập nhật tài khoản thành công.'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nguyen Van Test',
            'phone' => '0123456789',
        ]);
    }


    public function test_update_profile_validation_error()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST', '/api/profile', [
                '_method' => 'PUT',
                'name' => 'A',
                'phone' => str_repeat('1', 20),
                'avatar' => UploadedFile::fake()->create('file.txt', 10, 'text/plain'),
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'phone', 'avatar']);
    }
}