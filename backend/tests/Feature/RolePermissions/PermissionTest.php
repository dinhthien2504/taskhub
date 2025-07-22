<?php

namespace Tests\Feature\RolePermissions;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $permissions = [
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $user->givePermissionTo($permissions);
    }

    public function test_can_list_permissions_with_default_pagination()
    {
        for ($i = 1; $i <= 15; $i++) {
            Permission::create(['name' => 'Permission ' . $i]);
        }

        $response = $this->getJson('/api/permissions');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_can_list_permissions_with_custom_per_page()
    {
        for ($i = 1; $i <= 20; $i++) {
            Permission::create(['name' => 'Permission ' . $i]);
        }

        $response = $this->getJson('/api/permissions?per_page=5');

        $response->assertStatus(200);
        $response->assertJsonPath('meta.per_page', 5);
    }

    public function test_can_search_permissions_by_keyword()
    {
        Permission::create(['name' => 'Manage Users']);
        Permission::create(['name' => 'View Reports']);

        $response = $this->getJson('/api/permissions?search=Manage');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Manage Users']);
        $response->assertJsonMissing(['name' => 'View Reports']);
    }

    public function test_can_create_permission_with_valid_data()
    {
        $payload = [
            'name' => 'Edit Articles',
            'description' => 'Cho phép chỉnh sửa bài viết',
        ];

        $response = $this->postJson('/api/permissions', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Edit Articles']);
        $this->assertDatabaseHas('permissions', ['name' => 'Edit Articles']);
    }

    public function test_fails_to_create_permission_when_missing_name()
    {
        $response = $this->postJson('/api/permissions', [
            'description' => 'Không có tên',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_can_update_permission_with_valid_data()
    {
        $permission = Permission::create(['name' => 'Old Permission']);

        $response = $this->putJson("/api/permissions/{$permission->id}", [
            'name' => 'New Permission Name',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Cập nhật quyền thành công.']);
        $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'name' => 'New Permission Name']);
    }

    public function test_fails_to_update_permission_when_missing_name()
    {
        $permission = Permission::create(['name' => 'Old Permission']);

        $response = $this->putJson("/api/permissions/{$permission->id}", [
            'name' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_can_delete_permissions_without_roles()
    {
        $p1 = Permission::create(['name' => 'Permission 1']);
        $p2 = Permission::create(['name' => 'Permission 2']);

        $response = $this->deleteJson('/api/permissions', [
            'ids' => [$p1->id, $p2->id],
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['deleted_ids' => [$p1->id, $p2->id]]);
        $this->assertDatabaseMissing('permissions', ['id' => $p1->id]);
        $this->assertDatabaseMissing('permissions', ['id' => $p2->id]);
    }

    public function test_does_not_delete_permissions_with_attached_roles()
    {
        $permission = Permission::create(['name' => 'Attached Permission']);
        $role = Role::create(['name' => 'Test Role']);
        $role->givePermissionTo($permission);

        $response = $this->deleteJson('/api/permissions', [
            'ids' => [$permission->id],
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'deleted_ids' => [],
            'not_deleted_names' => 'Attached Permission'
        ]);
        $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
    }

    public function test_delete_non_existent_permissions_returns_empty_deleted_ids()
    {
        $response = $this->deleteJson('/api/permissions', ['ids' => [999]]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ids.0']);
        $response->assertJsonFragment([
            'message' => 'Quyền không tồn tại trong hệ thống.'
        ]);
    }
}
