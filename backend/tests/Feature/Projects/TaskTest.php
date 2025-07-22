<?php

namespace Tests\Feature\Projects;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected array $permissions = [
        'view any task',
        'create task',
        'update task',
        'delete task',
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
        Sanctum::actingAs($this->admin);
    }

    public function test_can_create_task_with_permission()
    {
        $project = Project::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $payload = [
            'title' => 'Test Task',
            'task_status_id' => $taskStatus->id,
        ];
        $response = $this->postJson(route('projects.tasks.store', $project), $payload);
        $response->assertCreated();
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'project_id' => $project->id,
        ]);
    }

    public function test_cannot_create_task_without_permission()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $project = Project::factory()->create();
        $payload = [
            'title' => 'Test Task',
            'task_status_id' => 1,
        ];
        $response = $this->postJson(route('projects.tasks.store', $project), $payload);
        $response->assertForbidden();
    }

    public function test_can_view_tasks_with_permission()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        $response = $this->getJson(route('projects.tasks.index', $project));
        $response->assertOk()->assertJsonFragment(['id' => $task->id]);
    }

    public function test_cannot_view_tasks_without_permission()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $project = Project::factory()->create();
        $response = $this->getJson(route('projects.tasks.index', $project));
        $response->assertForbidden();
    }

    public function test_can_update_task_with_permission()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        $payload = [
            'title' => 'Updated Task',
            'task_status_id' => 1,
        ];
        $response = $this->putJson(route('projects.tasks.update', [$project, $task->id]), $payload);
        $response->assertOk()->assertJsonFragment(['title' => 'Updated Task']);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
        ]);
    }

    public function test_cannot_update_task_without_permission()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        $payload = [
            'title' => 'Should Not Update',
            'task_status_id' => 1,
        ];
        $response = $this->putJson(route('projects.tasks.update', [$project, $task->id]), $payload);
        $response->assertForbidden();
    }

}