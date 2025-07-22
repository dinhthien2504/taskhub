<?php

namespace Tests\Feature\RolePermission;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $permissions = [
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view role permissions',
            'edit role permissions'
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $this->user->givePermissionTo($permissions);
    }

    public function test_can_list_roles_with_default_pagination()
    {
        for ($i = 1; $i <= 15; $i++) {
            Role::create(['name' => 'Role ' . $i, 'guard_name' => 'web']);
        }

        $response = $this->getJson('/api/roles');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_can_search_roles_by_keyword()
    {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);

        $response = $this->getJson('/api/roles?search=Admin');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Admin']);
        $response->assertJsonMissing(['name' => 'User']);
    }

    public function test_can_list_roles_with_custom_per_page()
    {
        for ($i = 1; $i <= 25; $i++) {
            Role::create(['name' => 'Role ' . $i, 'guard_name' => 'web']);
        }

        $response = $this->getJson('/api/roles?per_page=5');
        $response->assertStatus(200);
        $response->assertJsonPath('meta.per_page', 5);
    }

    public function test_can_create_role_with_valid_data()
    {
        $payload = [
            'name' => 'Manager',
            'description' => 'Quản lý chung',
        ];

        $response = $this->postJson('/api/roles', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Manager']);
        $this->assertDatabaseHas('roles', ['name' => 'Manager']);
    }

    public function test_fails_to_create_role_when_missing_name()
    {
        $response = $this->postJson('/api/roles', [
            'description' => 'Thiếu tên',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_can_update_role_with_valid_data()
    {
        $role = Role::create(['name' => 'Old Name']);

        $response = $this->putJson("/api/roles/{$role->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Cập nhật vai trò thành công.']);
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Updated Name']);
    }

    public function test_update_fails_when_role_does_not_exist()
    {
        $response = $this->putJson('/api/roles/999999', ['name' => 'Something']);
        $response->assertStatus(500);
    }

    public function test_fails_to_update_role_when_missing_name()
    {
        $role = Role::create(['name' => 'Old Name']);

        $response = $this->putJson("/api/roles/{$role->id}", [
            'name' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_can_delete_roles_without_users()
    {
        $role1 = Role::create(['name' => 'Admin']);
        $role2 = Role::create(['name' => 'User']);

        $response = $this->deleteJson('/api/roles', [
            'ids' => [$role1->id, $role2->id],
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['deleted_ids' => [$role1->id, $role2->id]]);
        $this->assertDatabaseMissing('roles', ['id' => $role1->id]);
    }

    public function test_does_not_delete_roles_with_attached_users()
    {
        $role = Role::create(['name' => 'Admin']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->deleteJson('/api/roles', [
            'ids' => [$role->id],
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'deleted_ids' => [],
            'not_deleted_names' => $role->name
        ]);
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    public function test_delete_fails_when_ids_are_missing()
    {
        $response = $this->deleteJson('/api/roles', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ids']);
    }

    public function test_delete_non_existent_roles_returns_validation_error()
    {
        $response = $this->deleteJson('/api/roles', ['ids' => [999]]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ids.0']);
        $response->assertJsonFragment([
            'message' => 'Vai trò không tồn tại trong hệ thống.'
        ]);
    }

    public function test_get_permission_assignment_successfully_returns_data()
    {
        $role = Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        $permissions = [];
        for ($i = 1; $i <= 3; $i++) {
            $permissions[] = Permission::create([
                'name' => 'permission_' . $i,
                'guard_name' => 'web',
            ]);
        }

        $permissionIds = array_map(fn($p) => $p->id, $permissions);
        $role->syncPermissions($permissionIds);

        $response = $this->getJson(route('roles.get-permission-assignment', $role->id));

        $response->assertOk()
            ->assertJsonStructure([
                'all_permissions' => [['id', 'name']],
                'role_permissions',
            ]);
    }

    public function test_update_permission_assignment_successfully_updates_permissions()
    {
        $role = Role::create(['name' => 'Editor']);

        $permissions = collect();
        for ($i = 1; $i <= 3; $i++) {
            $permissions->push(Permission::create([
                'name' => 'permission_' . $i,
                'guard_name' => 'web',
            ]));
        }

        $response = $this->putJson(
            route('roles.update-permission-assignment', $role->id),
            ['permission_ids' => $permissions->pluck('id')->toArray()]
        );

        $response->assertOk()
            ->assertJson(['message' => 'Cập nhật phân quyền thành công.']);

        $this->assertEqualsCanonicalizing(
            $permissions->pluck('id')->toArray(),
            $role->fresh()->permissions->pluck('id')->toArray()
        );
    }
}
