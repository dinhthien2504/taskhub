<?php

namespace Tests\Feature\Projects;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected array $permissions = [
        'create comment',
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

    public function test_can_add_comment_with_permission()
    {
        $task = Task::factory()->create();
        $payload = ['content' => 'Test comment'];
        $response = $this->postJson(route('tasks.comments.store', $task), $payload);
        $response->assertCreated();
        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'user_id' => $this->admin->id,
            'content' => 'Test comment',
        ]);
    }

    public function test_cannot_add_comment_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create();
        $payload = ['content' => 'Test comment'];
        $response = $this->postJson(route('tasks.comments.store', $task), $payload);
        $response->assertStatus(403);
    }
}