<?php

namespace Tests\Feature\Console;

use App\Jobs\SendCampaignJob;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendScheduledCampaignsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_jobs_for_scheduled_campaigns()
    {
        Queue::fake();

        $campaign1 = Campaign::factory()->create([
            'name' => 'Chiến dịch A',
            'scheduled_at' => now()->subMinutes(5),
            'status' => 'scheduled',
        ]);

        $campaign2 = Campaign::factory()->create([
            'name' => 'Chiến dịch B',
            'scheduled_at' => now()->subHour(),
            'status' => 'scheduled',
        ]);

        $this->artisan('campaigns:send')->assertExitCode(0);

        Queue::assertPushed(SendCampaignJob::class, function ($job) use ($campaign1) {
            return $job->campaign->id === $campaign1->id;
        });

        Queue::assertPushed(SendCampaignJob::class, function ($job) use ($campaign2) {
            return $job->campaign->id === $campaign2->id;
        });
    }

    public function test_does_not_dispatch_for_unscheduled_campaigns()
    {
        Queue::fake();

        Campaign::factory()->create([
            'name' => 'Không hợp lệ',
            'scheduled_at' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        Campaign::factory()->create([
            'name' => 'Đã gửi rồi',
            'scheduled_at' => now()->subDay(),
            'status' => 'sent',
        ]);

        $this->artisan('campaigns:send');

        Queue::assertNotPushed(SendCampaignJob::class);
    }
}
