<?php

namespace Tests\Unit\Users;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\DestroyUserRequest;
use App\Http\Requests\Users\AssignRoleRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data, string $formRequestClass)
    {
        $request = new $formRequestClass();
        return Validator::make($data, $request->rules(), $request->messages(), $request->attributes());
    }

    // --- STORE USER ---
    public function test_store_user_requires_valid_fields()
    {
        $data = [
            // thiếu name, email, password
        ];
        $validator = $this->validate($data, StoreUserRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
    }

    public function test_store_user_fails_with_invalid_email_and_password()
    {
        $data = [
            'name' => 'A',
            'email' => 'invalid-email',
            'password' => 'abc',
            'password_confirmation' => 'abc',
        ];
        $validator = $this->validate($data, StoreUserRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
    }

    public function test_store_user_passes_with_valid_data()
    {
        $data = [
            'name' => 'Nguyen Van Test',
            'email' => 'testuser@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];
        $validator = $this->validate($data, StoreUserRequest::class);
        $this->assertFalse($validator->fails());
    }

    // --- UPDATE USER ---
    public function test_update_user_requires_valid_name()
    {
        $data = ['name' => 'A'];
        $validator = $this->validate($data, UpdateUserRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    public function test_update_user_passes_with_valid_name()
    {
        $data = ['name' => 'Nguyen Van Update'];
        $validator = $this->validate($data, UpdateUserRequest::class);
        $this->assertFalse($validator->fails());
    }

    // --- DESTROY USER ---
    public function test_destroy_user_requires_ids_array()
    {
        $data = [];
        $validator = $this->validate($data, DestroyUserRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ids', $validator->errors()->messages());
    }

    public function test_destroy_user_fails_with_nonexistent_id()
    {
        $data = ['ids' => [99999]];
        $validator = $this->validate($data, DestroyUserRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ids.0', $validator->errors()->messages());
    }

    public function test_destroy_user_passes_with_valid_ids()
    {
        $user = User::factory()->create();
        $data = ['ids' => [$user->id]];
        $validator = $this->validate($data, DestroyUserRequest::class);
        $this->assertFalse($validator->fails());
    }

    // --- ASSIGN ROLE ---
    public function test_assign_role_requires_roles_array()
    {
        $data = [];
        $validator = $this->validate($data, AssignRoleRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('roles', $validator->errors()->messages());
    }

    public function test_assign_role_fails_with_invalid_role()
    {
        $data = ['roles' => ['not-exist']];
        $validator = $this->validate($data, AssignRoleRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('roles.0', $validator->errors()->messages());
    }

    public function test_assign_role_passes_with_existing_role()
    {
        Role::create(['name' => 'admin']);
        $data = ['roles' => ['admin']];
        $validator = $this->validate($data, AssignRoleRequest::class);
        $this->assertFalse($validator->fails());
    }
}