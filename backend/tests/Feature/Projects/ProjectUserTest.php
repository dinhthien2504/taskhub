<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected array $permissions = [
        'view project users',
        'add project user',
        'remove project user',
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

    public function test_can_list_project_users_with_permission()
    {
        $project = Project::factory()->create();
        $users = User::factory()->count(3)->create();
        $project->users()->attach($users);
        $response = $this->getJson(route('projects.users.index', $project));
        $response->assertOk()->assertJsonFragment(['id' => $users[0]->id]);
    }

    public function test_cannot_list_project_users_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $project = Project::factory()->create();
        $response = $this->getJson(route('projects.users.index', $project));
        $response->assertStatus(403);
    }

    public function test_can_assign_users_to_project_with_permission()
    {
        $project = Project::factory()->create();
        $users = User::factory()->count(2)->create();
        $payload = ['user_ids' => $users->pluck('id')->toArray()];
        $response = $this->postJson(route('projects.users.store', $project), $payload);
        $response->assertStatus(201)->assertJson(['message' => 'Người dùng đã được gán']);
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $users[0]->id,
        ]);
    }

    public function test_cannot_assign_users_to_project_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $project = Project::factory()->create();
        $users = User::factory()->count(2)->create();
        $payload = ['user_ids' => $users->pluck('id')->toArray()];
        $response = $this->postJson(route('projects.users.store', $project), $payload);
        $response->assertStatus(403);
    }

    public function test_can_remove_user_from_project_with_permission()
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $project->users()->attach($user);
        $response = $this->deleteJson(route('projects.users.destroy', [$project, $user]));
        $response->assertOk()->assertJson(['message' => 'Người dùng đã bị xoá khỏi dự án']);
        $this->assertDatabaseMissing('project_user', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_remove_user_from_project_without_permission()
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $project->users()->attach($user);
        $other = User::factory()->create();
        $this->actingAs($other);
        $response = $this->deleteJson(route('projects.users.destroy', [$project, $user]));
        $response->assertStatus(403);
    }
}