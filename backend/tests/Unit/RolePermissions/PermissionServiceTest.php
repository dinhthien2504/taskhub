<?php

namespace Tests\Unit\RolePermissions;

use App\Repositories\PermissionEloquentRepository;
use App\Services\RolePermissions\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $repo = new PermissionEloquentRepository();
        $this->service = new PermissionService($repo);
    }

    public function test_create_permission_success()
    {
        $request = new Request([
            'name' => 'create-posts',
            'description' => 'Can create posts'
        ]);

        $result = $this->service->createPermission($request);

        $this->assertEquals('create-posts', $result->name);
        $this->assertDatabaseHas('permissions', ['name' => 'create-posts']);
    }

    public function test_update_permission_success()
    {
        $permission = Permission::create(['name' => 'edit-old']);

        $request = new Request([
            'name' => 'edit-new',
            'description' => 'Updated'
        ]);

        $this->service->updatePermission($request, $permission->id);

        $this->assertDatabaseHas('permissions', ['name' => 'edit-new']);
    }

    public function test_delete_permissions_only_deletes_unlinked()
    {
        $canDelete = Permission::create(['name' => 'deletable']);
        $cannotDelete = Permission::create(['name' => 'undeletable']);
        $role = Role::create(['name' => 'Admin']);
        $cannotDelete->assignRole($role);

        $result = $this->service->deletePermissions([$canDelete->id, $cannotDelete->id]);

        $this->assertEquals([$canDelete->id], $result['deleted_ids']);
        $this->assertStringContainsString('undeletable', $result['not_deleted_names']);
        $this->assertDatabaseMissing('permissions', ['id' => $canDelete->id]);
        $this->assertDatabaseHas('permissions', ['id' => $cannotDelete->id]);
    }

    public function test_get_all_permission_returns_results()
    {
        Permission::create(['name' => 'view-permission']);
        $permissions = $this->service->getAllPermissions();
        $this->assertNotEmpty($permissions);
    }
}
