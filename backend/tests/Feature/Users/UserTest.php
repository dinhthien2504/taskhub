<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected array $permissions = [
        'view users',
        'create users',
        'edit users',
        'delete users',
        'view user roles',
        'assign roles to users',
        'restore users'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        foreach ($this->permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm]);
        }

        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo($this->permissions);
        $this->admin->assignRole('admin');
        $this->actingAs($this->admin);
    }

    public function test_can_list_users_with_default_pagination()
    {
        User::factory()->count(15)->create();
        $response = $this->getJson('/api/users');
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_can_search_users_by_keyword()
    {
        User::factory()->create(['name' => 'Alice']);
        User::factory()->create(['name' => 'Bob']);

        $response = $this->getJson('/api/users?search=Alice');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Alice']);
        $response->assertJsonMissing(['name' => 'Bob']);
    }

    public function test_can_list_users_with_custom_per_page()
    {
        User::factory()->count(12)->create();
        $response = $this->getJson('/api/users?per_page=5');
        $response->assertStatus(200);
        $this->assertEquals(5, $response->json('meta.per_page'));
    }

    public function test_can_create_user_with_valid_data()
    {
        $payload = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'Password1@',
            'password_confirmation' => 'Password1@'
        ];
        $response = $this->postJson('/api/users', $payload);
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'New User']);
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_fails_to_create_user_when_missing_required_fields()
    {
        $response = $this->postJson('/api/users', [
            'name' => '',
            'email' => '',
            'password' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    // 3. Cập nhật người dùng
    public function test_can_update_user_with_valid_data()
    {
        $user = User::factory()->create();
        $payload = ['name' => 'Updated User'];
        $response = $this->putJson("/api/users/{$user->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Cập nhật người dùng thành công.']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated User']);
    }

    public function test_update_fails_when_user_does_not_exist()
    {
        $response = $this->putJson('/api/users/999999', ['name' => 'Something']);
        $response->assertStatus(500);
    }

    public function test_fails_to_update_user_when_missing_name()
    {
        $user = User::factory()->create();
        $response = $this->putJson("/api/users/{$user->id}", ['name' => '']);
        $response->assertStatus(422);
    }

    // 4. Cập nhật trạng thái
    public function test_can_update_user_status()
    {
        $user = User::factory()->create(['active' => false]);
        $response = $this->patchJson("/api/users/{$user->id}/status", ['active' => true]);
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Cập nhật trạng thái thành công.']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'active' => true]);
    }

    // 5. Xoá người dùng
    public function test_can_delete_users_that_are_not_admin()
    {
        $user = User::factory()->create();
        $response = $this->deleteJson('/api/users', ['ids' => [$user->id]]);
        $response->assertStatus(200)
            ->assertJsonFragment(['deleted_ids' => [$user->id]]);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_does_not_delete_admin_or_superadmin_users()
    {
        $admin = User::factory()->create(['name' => 'Super Admin']);
        $admin->assignRole('admin');
        $response = $this->deleteJson('/api/users', ['ids' => [$admin->id]]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['deleted_ids' => []]);
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }

    public function test_delete_fails_when_ids_are_missing()
    {
        $response = $this->deleteJson('/api/users', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ids']);
    }

    // 6. Phân quyền
    public function test_can_get_user_roles()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'editor']);
        $user->assignRole('editor');

        $response = $this->getJson("/api/users/{$user->id}/roles");
        $response->assertStatus(200)
            ->assertJsonStructure(['assigned_roles', 'all_roles']);
        $this->assertEquals(['editor'], $response->json('assigned_roles'));
    }

    public function test_can_assign_roles_to_user()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'editor']);

        $response = $this->putJson("/api/users/{$user->id}/roles", ['roles' => ['editor']]);
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Cập nhật vai trò thành công.']);
        $this->assertTrue($user->fresh()->hasRole('editor'));
    }

    // 7. Thùng rác và khôi phục
    public function test_can_list_trashed_users()
    {
        $user = User::factory()->create();
        $user->delete();
        $response = $this->getJson('/api/users/trashed');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function test_can_restore_deleted_user()
    {
        $user = User::factory()->create();
        $user->delete();
        $response = $this->putJson("/api/users/{$user->id}/restore");
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Người dùng đã được khôi phục thành công.']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
    }

    public function test_restore_fails_when_user_does_not_exist()
    {
        $response = $this->putJson("/api/users/999999/restore");
        $response->assertStatus(500);
    }
}