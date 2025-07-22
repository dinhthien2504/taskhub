<?php

namespace Tests\Feature\WorkingTime;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkingTimeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Permission::firstOrCreate(['name' => 'view working time']);
        Permission::firstOrCreate(['name' => 'edit working time']);

        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo(['view working time', 'edit working time']);
        $this->admin->assignRole('admin');

        $this->actingAs($this->admin);
    }

    public function test_admin_can_view_working_time()
    {
        $response = $this->getJson('/api/working-time');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'start_time', 'late_after', 'end_time'], 
            ]);
    }

    public function test_admin_can_upsert_working_time()
    {
        $data = [
            'start_time' => '08:00',
            'late_after' => '08:15',
            'end_time' => '17:00',
        ];

        $response = $this->putJson('/api/working-time', $data);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'start_time' => '08:00',
                'late_after' => '08:15',
                'end_time' => '17:00',
            ]);

        $this->assertDatabaseHas('working_times', [
            'start_time' => '08:00',
            'late_after' => '08:15',
            'end_time' => '17:00',
        ]);
    }

    public function test_upsert_fails_with_invalid_time()
    {
        $data = [
            'start_time' => '08:00',
            'late_after' => '07:30',
        ];

        $response = $this->putJson('/api/working-time', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['late_after']);
    }

    public function test_user_without_permission_cannot_view_working_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/working-time');
        $response->assertStatus(403);
    }

    public function test_user_without_permission_cannot_update_working_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->putJson('/api/working-time', [
            'start_time' => '08:00',
            'late_after' => '08:30',
        ]);

        $response->assertStatus(403);
    }
}
