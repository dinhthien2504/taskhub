<?php

namespace Tests\Feature\Campaigns;

use App\Models\Campaign;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected array $permissions = [
        'view campaigns',
        'create campaigns',
        'update campaigns',
        'delete campaigns',
        'restore campaigns',
        'view trashed campaigns',
        'send campaigns',
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

    public function test_can_list_campaigns_with_permission()
    {
        Campaign::factory()->count(2)->create(['created_by' => $this->admin->id]);
        $response = $this->getJson('/api/campaigns');
        $response->assertOk();
        $this->assertGreaterThanOrEqual(2, count($response->json('data')));
    }

    public function test_can_create_campaign()
    {
        $template = EmailTemplate::factory()->create();
        $payload = [
            'name' => 'Test Campaign',
            'description' => 'Test description',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending',
            'template_id' => $template->id
        ];
        $response = $this->postJson('/api/campaigns', $payload);
        $response->assertCreated();
        $this->assertDatabaseHas('campaigns', ['name' => 'Test Campaign']);
    }

    public function test_can_update_campaign()
    {
        $template = EmailTemplate::factory()->create();
        $campaign = Campaign::factory()->create([
            'created_by' => $this->admin->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'template_id' => $template->id
        ]);

        $payload = [
            'name' => 'Updated Campaign',
            'description' => 'Updated description',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(), // đảm bảo > start_date
            'status' => 'pending',
            'template_id' => $template->id
        ];

        $response = $this->putJson('/api/campaigns/' . $campaign->id, $payload);
        $response->assertOk();
        $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'name' => 'Updated Campaign']);
    }

    public function test_can_delete_campaign()
    {
        $campaign = Campaign::factory()->create(['created_by' => $this->admin->id]);
        $response = $this->deleteJson('/api/campaigns/' . $campaign->id);
        $response->assertOk();
        $this->assertSoftDeleted('campaigns', ['id' => $campaign->id]);
    }

    public function test_can_restore_campaign()
    {
        $campaign = Campaign::factory()->create(['created_by' => $this->admin->id]);
        $campaign->delete();
        $response = $this->putJson('/api/campaigns/' . $campaign->id . '/restore');
        $response->assertOk();
        $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'deleted_at' => null]);
    }

    public function test_can_view_trashed_campaigns()
    {
        $campaign = Campaign::factory()->create(['created_by' => $this->admin->id]);
        $campaign->delete();
        $response = $this->getJson('/api/campaigns/trashed');
        $response->assertOk();
        $this->assertTrue(collect($response->json())->contains('id', $campaign->id));
    }

    public function test_can_send_campaign()
    {
        $campaign = Campaign::factory()->create(['created_by' => $this->admin->id, 'status' => 'pending']);
        $response = $this->postJson('/api/campaigns/' . $campaign->id . '/send');
        $response->assertOk();

        $campaign->refresh();
        $this->assertTrue(in_array($campaign->status, ['sending', 'sent']));
    }
}