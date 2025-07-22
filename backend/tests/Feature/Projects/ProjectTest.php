<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected array $permissions = [
        'view any project',
        'create project',
        'update project',
        'update project status',
        'delete project',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo role và permission
        Role::firstOrCreate(['name' => 'admin']);
        foreach ($this->permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Tạo user và gán quyền
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo($this->permissions);
        $this->admin->assignRole('admin');
        $this->actingAs($this->admin);
    }

    public function test_can_list_projects_with_permission()
    {
        Project::factory()->count(5)->create(['owner_id' => $this->admin->id]);
        $response = $this->getJson('/api/projects');
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_cannot_list_projects_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->getJson('/api/projects');
        $response->assertStatus(403);
    }

    public function test_can_create_project_with_permission()
    {
        $payload = [
            'name' => 'Project A',
            'owner_id' => $this->admin->id,
            'description' => 'Test project'
        ];
        $response = $this->postJson('/api/projects', $payload);
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Project A']);
        $this->assertDatabaseHas('projects', ['name' => 'Project A']);
    }

    public function test_cannot_create_project_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $payload = [
            'name' => 'Project B',
            'owner_id' => $user->id,
        ];
        $response = $this->postJson('/api/projects', $payload);
        $response->assertStatus(403);
    }

    public function test_can_update_project_with_permission()
    {
        $project = Project::factory()->create(['owner_id' => $this->admin->id]);
        $payload = [
            'name' => 'Updated Project',
            'owner_id' => $this->admin->id,
            'description' => 'Updated description'
        ];
        $response = $this->putJson("/api/projects/{$project->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Project']);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated Project']);
    }

    public function test_cannot_update_project_without_permission()
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);
        $payload = [
            'name' => 'Should Not Update',
            'owner_id' => $user->id,
        ];
        $response = $this->putJson("/api/projects/{$project->id}", $payload);
        $response->assertStatus(403);
    }

    public function test_can_update_project_status_with_permission()
    {
        $project = Project::factory()->create(['owner_id' => $this->admin->id, 'status' => 'in_progress']);
        $response = $this->patchJson("/api/projects/{$project->id}", ['status' => 'completed']);
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Cập nhật trạng thái thành công.']);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'completed']);
    }

    public function test_cannot_update_project_status_without_permission()
    {
        $project = Project::factory()->create(['status' => 'in_progress']);
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->patchJson("/api/projects/{$project->id}", ['status' => 'completed']);
        $response->assertStatus(403);
    }

    public function test_cannot_delete_project_without_permission()
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->deleteJson("/api/projects/{$project->id}");
        $response->assertStatus(403);
    }

    public function test_fails_to_create_project_when_missing_required_fields()
    {
        $response = $this->postJson('/api/projects', [
            'name' => '',
            'owner_id' => '',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'owner_id']);
    }

    public function test_fails_to_update_project_when_missing_name()
    {
        $project = Project::factory()->create(['owner_id' => $this->admin->id]);
        $payload = [
            'name' => '',
            'owner_id' => $this->admin->id,
        ];
        $response = $this->putJson("/api/projects/{$project->id}", $payload);
        $response->assertStatus(422);
    }

    public function test_update_fails_when_project_does_not_exist()
    {
        $payload = [
            'name' => 'Something',
            'owner_id' => $this->admin->id,
        ];
        $response = $this->putJson('/api/projects/999999', $payload);
        $response->assertStatus(500);
    }
}