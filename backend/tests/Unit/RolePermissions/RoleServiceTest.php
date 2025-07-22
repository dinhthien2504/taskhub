<?php

namespace Tests\Unit\RolePermissions;

use App\Repositories\PermissionEloquentRepository;
use App\Repositories\RoleEloquentRepository;
use App\Services\RolePermissions\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RoleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $roleRepository = new RoleEloquentRepository();
        $permissionRepository = new PermissionEloquentRepository();
        $this->service = new RoleService($roleRepository, $permissionRepository);
    }

    public function test_create_role_success()
    {
        $request = new Request([
            'name' => 'Manager',
            'description' => 'Role for managers'
        ]);

        $role = $this->service->createRole($request);

        $this->assertEquals('Manager', $role->name);
        $this->assertDatabaseHas('roles', ['name' => 'Manager']);
    }

    public function test_update_role_success()
    {
        $role = Role::create(['name' => 'OldRole']);

        $request = new Request([
            'name' => 'UpdatedRole',
            'description' => 'Updated description'
        ]);

        $updated = $this->service->updateRole($request, $role->id);

        $this->assertEquals('UpdatedRole', $updated->name);
    }

    public function test_delete_roles_only_unlinked()
    {
        $canDelete = Role::create(['name' => 'DeleteMe']);
        $cannotDelete = Role::create(['name' => 'Assigned']);
        $user = \App\Models\User::factory()->create();
        $user->assignRole($cannotDelete);

        $result = $this->service->deleteRoles([$canDelete->id, $cannotDelete->id]);

        $this->assertEquals([$canDelete->id], $result['deleted_ids']);
        $this->assertStringContainsString('Assigned', $result['not_deleted_names']);
        $this->assertDatabaseMissing('roles', ['id' => $canDelete->id]);
        $this->assertDatabaseHas('roles', ['id' => $cannotDelete->id]);
    }

    public function test_get_all_roles()
    {
        Role::create(['name' => 'User']);
        $result = $this->service->getAllRoles();

        $this->assertNotEmpty($result);
    }

    public function test_get_permission_assignment_returns_correct_data()
    {
        $role = Role::create(['name' => 'Manager']);
        $perm1 = Permission::create(['name' => 'edit', 'guard_name' => 'web']);
        $perm2 = Permission::create(['name' => 'view', 'guard_name' => 'web']);

        $role->syncPermissions([$perm1->id]);

        $result = $this->service->getPermissionAssignment($role->id);

        $this->assertArrayHasKey('all_permissions', $result);
        $this->assertArrayHasKey('role_permissions', $result);

        $this->assertContains($perm1->id, $result['role_permissions']);
        $this->assertNotContains($perm2->id, $result['role_permissions']);
        $this->assertCount(2, $result['all_permissions']);
    }

    public function test_update_permission_assignment_syncs_correctly()
    {
        $role = Role::create(['name' => 'Editor']);
        $perm1 = Permission::create(['name' => 'create', 'guard_name' => 'web']);
        $perm2 = Permission::create(['name' => 'delete', 'guard_name' => 'web']);

        $this->service->updatePermissionAssignment($role->id, [$perm1->id, $perm2->id]);

        $this->assertEqualsCanonicalizing(
            [$perm1->id, $perm2->id],
            $role->fresh()->permissions->pluck('id')->toArray()
        );
    }

}
