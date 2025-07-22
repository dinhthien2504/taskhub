<?php

namespace Tests\Unit\Campaigns;

use App\Models\Campaign;
use App\Repositories\CampaignEloquentRepository;
use App\Services\Campaigns\CampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CampaignServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CampaignService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $repo = new CampaignEloquentRepository();
        $this->service = new CampaignService($repo);
    }

    public function test_create_campaign_success()
    {
        $template = Campaign::factory()->create();
        $request = new Request([
            'name' => 'Chiến dịch test',
            'description' => 'Mô tả',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending',
            'template_id' => $template->id
        ]);
        $campaign = $this->service->createCampaign($request);

        $this->assertEquals('Chiến dịch test', $campaign->name);
        $this->assertDatabaseHas('campaigns', [
            'name' => 'Chiến dịch test',
        ]);
    }

    public function test_update_campaign_success()
    {
        $campaign = Campaign::factory()->create(['name' => 'Cũ', 'status' => 'pending']);
        $request = new Request([
            'name' => 'Mới',
            'description' => 'Đã cập nhật',
            'start_date' => $campaign->start_date,
            'end_date' => $campaign->end_date,
            'status' => $campaign->status,
        ]);
        $updated = $this->service->updateCampaign($request, $campaign->id);

        $this->assertEquals('Mới', $updated->name);
        $this->assertEquals('Đã cập nhật', $updated->description);
    }

    public function test_update_campaign_not_found()
    {
        $request = new Request([
            'name' => 'Bất kỳ',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending',
        ]);
        $this->expectException(\Exception::class);
        $this->service->updateCampaign($request, 999);
    }

    public function test_delete_campaign_success()
    {
        $campaign = Campaign::factory()->create();
        $this->service->deleteCampaign($campaign->id);
        $this->assertSoftDeleted('campaigns', ['id' => $campaign->id]);
    }

    public function test_restore_campaign_success()
    {
        $campaign = Campaign::factory()->create();
        $campaign->delete();
        $restored = $this->service->restoreCampaign($campaign->id);
        $this->assertEquals($campaign->id, $restored['id']);
        $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'deleted_at' => null]);
    }
}
