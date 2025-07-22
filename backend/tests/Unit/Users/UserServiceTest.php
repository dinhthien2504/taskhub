<?php

namespace Tests\Unit\Users;

use App\Models\Role;
use App\Models\User;
use App\Repositories\UserEloquentRepository;
use App\Services\Users\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $repo = new UserEloquentRepository();
        $this->service = new UserService($repo);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
    }

    public function test_create_user_success()
    {
        $request = new Request([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!'
        ]);

        $user = $this->service->createUser($request);

        $this->assertEquals('Test User', $user->name);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);
    }

    public function test_update_user_success()
    {
        $user = User::factory()->create(['name' => 'Old Name', 'active' => 0]);

        $request = new Request([
            'name' => 'New Name',
            'active' => 1
        ]);

        $updated = $this->service->updateUser($request, $user->id);

        $this->assertEquals('New Name', $updated->name);
        $this->assertEquals(1, $updated->active);
    }

    public function test_update_user_not_found()
    {
        $request = new Request(['name' => 'Anything']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Người dùng không tồn tại.');
        $this->service->updateUser($request, 999);
    }

    public function test_delete_users_only_non_admin()
    {
        $user1 = User::factory()->create(['name' => 'CanDelete']);
        $user2 = User::factory()->create(['name' => 'CannotDelete']);
        $user2->assignRole('admin');

        $result = $this->service->deleteUsers([$user1->id, $user2->id]);

        $this->assertEquals([$user1->id], $result['deleted_ids']);
        $this->assertStringContainsString('CannotDelete', $result['not_deleted_names']);

        $this->assertSoftDeleted('users', ['id' => $user1->id]);
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
    }


    public function test_get_all_users()
    {
        $loggedInUser = User::factory()->create();
        $loggedInUser->assignRole('admin');

        $this->actingAs($loggedInUser);

        User::factory()->count(2)->create();

        $users = $this->service->getAllUsers();

        $this->assertGreaterThanOrEqual(2, $users->count());
    }

    public function test_assign_roles()
    {
        $user = User::factory()->create();
        $this->service->assignRoles($user->id, ['admin']);
        $this->assertTrue($user->fresh()->hasRole('admin'));
    }

    public function test_restore_user()
    {
        $user = User::factory()->create();
        $user->delete();
        $this->assertSoftDeleted('users', ['id' => $user->id]);

        $result = $this->service->restoreUser($user->id);
        $this->assertFalse($result['user']->trashed());
    }
}
