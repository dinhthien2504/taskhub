<?php

namespace Tests\Unit\RolePermissions;

use App\Http\Requests\RolePermissions\{StoreRoleRequest, UpdateRoleRequest, DestroyRoleRequest};
use App\Http\Requests\RolePermissions\UpdateRolePermissionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data, string $formRequestClass)
    {
        $request = new $formRequestClass();
        return Validator::make($data, $request->rules(), $request->messages(), $request->attributes());
    }

    // --- STORE ---
    public function test_store_requires_name()
    {
        $data = ['description' => 'Test role'];
        $validator = $this->validate($data, StoreRoleRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    public function test_store_name_must_be_unique()
    {
        Role::create(['name' => 'existing.role']);
        $data = ['name' => 'existing.role'];

        $validator = $this->validate($data, StoreRoleRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    public function test_store_valid_data_passes()
    {
        $data = ['name' => 'role.create', 'description' => 'Role creation'];
        $validator = $this->validate($data, StoreRoleRequest::class);

        $this->assertFalse($validator->fails());
    }

    // --- UPDATE ---
    public function test_update_valid_data_passes()
    {
        $data = ['name' => 'role.edit', 'description' => 'Edit role'];
        $validator = $this->validate($data, UpdateRoleRequest::class);

        $this->assertFalse($validator->fails());
    }

    public function test_update_description_too_long_fails()
    {
        $data = ['name' => 'role.edit', 'description' => str_repeat('a', 256)];
        $validator = $this->validate($data, UpdateRoleRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('description', $validator->errors()->messages());
    }

    // --- DESTROY ---
    public function test_destroy_requires_ids_array()
    {
        $data = ['ids' => null];
        $validator = $this->validate($data, DestroyRoleRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ids', $validator->errors()->messages());
    }

    public function test_destroy_fails_with_nonexistent_id()
    {
        $data = ['ids' => [999]];
        $validator = $this->validate($data, DestroyRoleRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ids.0', $validator->errors()->messages());
    }

    public function test_destroy_valid_ids_passes()
    {
        $role = Role::create(['name' => 'Tester']);
        $data = ['ids' => [$role->id]];

        $validator = $this->validate($data, DestroyRoleRequest::class);

        $this->assertFalse($validator->fails());
    }

    public function test_update_permission_requires_permission_ids_array()
    {
        $data = [];
        $validator = $this->validate($data, UpdateRolePermissionRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('permission_ids', $validator->errors()->messages());
    }

    public function test_update_permission_ids_must_be_integers_and_exist()
    {
        $data = ['permission_ids' => ['not-an-int']];
        $validator = $this->validate($data, UpdateRolePermissionRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('permission_ids.0', $validator->errors()->messages());
    }

    public function test_update_permission_valid_data_passes()
    {
        $permission1 = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);
        $permission2 = Permission::create(['name' => 'delete articles', 'guard_name' => 'web']);

        $data = ['permission_ids' => [$permission1->id, $permission2->id]];

        $validator = $this->validate($data, UpdateRolePermissionRequest::class);

        $this->assertFalse($validator->fails());
    }
}
