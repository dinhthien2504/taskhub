<?php

namespace Tests\Feature\CheckIns;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected array $permissions = [
        'view check in logs',
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

        $this->user = User::factory()->create();
    }

    public function test_admin_can_view_check_in_logs()
    {
        $response = $this->getJson('/api/check-in-logs');
        $response->assertStatus(200);
    }

    public function test_admin_can_export_check_in_logs_as_csv()
    {
        $response = $this->get('/api/check-in-logs/export');

        $response->assertStatus(200);

        $this->assertStringStartsWith('text/csv', $response->headers->get('Content-Type'));

        $response->assertHeader('Content-Disposition', 'attachment; filename="check_in_logs.csv"');
    }


    public function test_user_without_permission_cannot_view_logs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/check-in-logs');
        $response->assertStatus(403);
    }

    public function test_user_without_permission_cannot_export_logs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/api/check-in-logs/export');
        $response->assertStatus(403);
    }

    public function test_can_check_in_successfully()
    {
        $this->actingAs($this->user);
        $response = $this->postJson('/api/check-in');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'check_in_time',
                'check_out_time',
                'status'
            ]);

        $this->assertDatabaseHas('check_in_logs', [
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_check_out_successfully()
    {
         $this->actingAs($this->user);
        $this->postJson('/api/check-in');

        $response = $this->postJson('/api/check-out');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'check_in_time',
                'check_out_time',
                'status'
            ]);

        $this->assertDatabaseHas('check_in_logs', [
            'user_id' => $this->user->id,
            'check_out_time' => now(),
        ]);
    }

    public function test_get_today_log_successfully()
    {
        $this->postJson('/api/check-in');

        $response = $this->getJson('/api/today-log');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'check_in_time',
                'check_out_time',
                'status'
            ]);
    }

    public function test_check_out_fails_if_not_checked_in()
    {
        $response = $this->postJson('/api/check-out');

        $response->assertStatus(500)
            ->assertJsonFragment([
                'messsage' => 'Check out thất bại.'
            ]);
    }

    public function test_check_in_fails_for_duplicate_check_in()
    {
        $this->postJson('/api/check-in');
        $response = $this->postJson('/api/check-in');

        $response->assertStatus(500)
            ->assertJsonFragment([
                'messsage' => 'Check in thất bại.'
            ]);
    }
}
