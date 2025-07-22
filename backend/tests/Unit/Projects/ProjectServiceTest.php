<?php

namespace Tests\Unit\Projects;

use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectEloquentRepository;
use App\Services\Projects\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProjectService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $repo = new ProjectEloquentRepository();
        $this->service = new ProjectService($repo);
    }

    public function test_create_project_success()
    {
        $user = User::factory()->create();
        $request = new Request([
            'name' => 'Dự án test',
            'owner_id' => $user->id,
            'description' => 'Mô tả'
        ]);
        $project = $this->service->createProject($request);

        $this->assertEquals('Dự án test', $project->name);
        $this->assertDatabaseHas('projects', [
            'name' => 'Dự án test',
            'owner_id' => $user->id
        ]);
    }

    public function test_update_project_success()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['name' => 'Cũ', 'owner_id' => $user->id]);
        $request = new Request([
            'name' => 'Mới',
            'owner_id' => $user->id,
            'description' => 'Đã cập nhật'
        ]);
        $updated = $this->service->updateProject($request, $project->id);

        $this->assertEquals('Mới', $updated->name);
        $this->assertEquals('Đã cập nhật', $updated->description);
    }

    public function test_update_project_not_found()
    {
        $request = new Request(['name' => 'Bất kỳ', 'owner_id' => 1]);
        $this->expectException(\Exception::class);
        $this->service->updateProject($request, 999);
    }

    public function test_update_project_status()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id, 'status' => 'in_progress']);
        $request = new Request(['status' => 'completed']);
        $this->service->updateProjectStatus($request, $project->id);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => 'completed'
        ]);
    }

    public function test_get_all_projects()
    {
        $user = User::factory()->create();
        Project::factory()->count(2)->create(['owner_id' => $user->id]);
        $projects = $this->service->getAllProjects(null, 10, 'in_progress');
        $this->assertGreaterThanOrEqual(2, $projects->count());
    }
}