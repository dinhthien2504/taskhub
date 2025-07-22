<?php

namespace Tests\Feature\Campaigns;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CampaignUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Campaign $campaign;
    protected array $permissions = [
        'view campaign users',
        'assign campaign users',
        'remove campaign users',
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
        $this->campaign = Campaign::factory()->create(['created_by' => $this->admin->id]);
    }

    public function test_can_list_assigned_users()
    {
        $users = User::factory()->count(2)->create();
        $this->campaign->users()->sync($users->pluck('id')->toArray());
        $response = $this->getJson("/api/campaigns/{$this->campaign->id}/users");
        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_assign_users_to_campaign()
    {
        $users = User::factory()->count(2)->create();
        $payload = ['user_ids' => $users->pluck('id')->toArray()];
        $response = $this->postJson("/api/campaigns/{$this->campaign->id}/users", $payload);
        $response->assertOk();
        $this->assertEquals(2, $this->campaign->users()->count());
    }

    public function test_cannot_assign_users_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $payload = ['user_ids' => [$user->id]];
        $response = $this->postJson("/api/campaigns/{$this->campaign->id}/users", $payload);
        $response->assertStatus(403);
    }
}
