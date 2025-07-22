<?php

namespace Tests\Unit\RolePermissions;

use App\Http\Requests\RolePermissions\StorePermissionRequest;
use App\Http\Requests\RolePermissions\UpdatePermissionRequest;
use App\Http\Requests\RolePermissions\DestroyPermissionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionRequestTest extends TestCase
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
        $data = ['description' => 'Test permission'];

        $validator = $this->validate($data, StorePermissionRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    public function test_store_name_must_be_unique()
    {
        Permission::create(['name' => 'duplicate.permission']);

        $data = ['name' => 'duplicate.permission'];

        $validator = $this->validate($data, StorePermissionRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    public function test_store_valid_data_passes()
    {
        $data = ['name' => 'permission.create', 'description' => 'This is a permission.'];

        $validator = $this->validate($data, StorePermissionRequest::class);

        $this->assertFalse($validator->fails());
    }

    // --- UPDATE ---

    public function test_update_valid_data_passes()
    {
        $data = ['name' => 'permission.edit', 'description' => 'Update description'];

        $validator = $this->validate($data, UpdatePermissionRequest::class);

        $this->assertFalse($validator->fails());
    }

    public function test_update_description_too_long_fails()
    {
        $data = ['name' => 'permission.edit', 'description' => str_repeat('a', 256)];

        $validator = $this->validate($data, UpdatePermissionRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('description', $validator->errors()->messages());
    }

    // --- DESTROY ---

    public function test_destroy_requires_ids_array()
    {
        $data = ['ids' => null];

        $validator = $this->validate($data, DestroyPermissionRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ids', $validator->errors()->messages());
    }

    public function test_destroy_fails_with_nonexistent_id()
    {
        $data = ['ids' => [999]];

        $validator = $this->validate($data, DestroyPermissionRequest::class);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ids.0', $validator->errors()->messages());
    }

    public function test_destroy_valid_ids_passes()
    {
        $permission = Permission::create(['name' => 'Viewer']);

        $data = ['ids' => [$permission->id]];

        $validator = $this->validate($data, DestroyPermissionRequest::class);

        $this->assertFalse($validator->fails());
    }
}
