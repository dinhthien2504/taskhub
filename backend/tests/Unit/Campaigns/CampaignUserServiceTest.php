<?php

namespace Tests\Unit\Campaigns;

use App\Models\Campaign;
use App\Models\User;
use App\Repositories\CampaignEloquentRepository;
use App\Services\Campaigns\CampaignUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CampaignUserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CampaignUserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $repo = new CampaignEloquentRepository();
        $this->service = new CampaignUserService($repo);
    }

    public function test_get_assigned_users_success()
    {
        $campaign = Campaign::factory()->create();
        $users = User::factory()->count(2)->create();
        $campaign->users()->sync($users->pluck('id')->toArray());
        $result = $this->service->getAssignedUsers($campaign);
        $this->assertCount(2, $result);
    }

    public function test_assign_users_success()
    {
        $campaign = Campaign::factory()->create();
        $users = User::factory()->count(2)->create();
        $result = $this->service->assignUsers($campaign->id, $users->pluck('id')->toArray());
        $this->assertCount(2, $result->users);
    }

    public function test_assign_users_campaign_not_found()
    {
        $users = User::factory()->count(2)->create();
        $this->expectException(\Exception::class);
        $this->service->assignUsers(999, $users->pluck('id')->toArray());
    }
}
