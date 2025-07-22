<?php

namespace Tests\Feature\Logs;

use App\Models\User;
use App\Models\UserActionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected array $permissions = [
        'view user activity logs',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        foreach ($this->permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo($this->permissions);
        $this->admin->assignRole('admin');
        $this->actingAs($this->admin);
    }

    public function test_can_list_user_activity_logs_with_permission()
    {
        UserActionLog::factory()->count(3)->create(['user_id' => $this->admin->id, 'action' => 'test_action']);
        $response = $this->getJson(route('users.activity-logs', ['search' => 'test_action']));
        $this->assertCount(3, $response->json('data'));
    }

    public function test_cannot_list_user_activity_logs_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->getJson(route('users.activity-logs'));
        $response->assertStatus(403);
    }

    public function test_can_filter_user_activity_logs_by_search()
    {
        UserActionLog::factory()->create(['user_id' => $this->admin->id, 'action' => 'login', 'description' => 'User logged in.']);
        UserActionLog::factory()->create(['user_id' => $this->admin->id, 'action' => 'update', 'description' => 'User updated profile.']);
        $response = $this->getJson(route('users.activity-logs', ['search' => 'login']));
        $response->assertOk()->assertJsonFragment(['action' => 'login']);
    }
}
