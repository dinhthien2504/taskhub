<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Jobs\SendCampaignJob;

class SendScheduledCampaigns extends Command
{
    protected $signature = 'campaigns:send';
    protected $description = 'Send scheduled campaign emails';

    public function handle()
    {
        $this->info("Bắt đầu tìm campaign để gửi...");
        $campaigns = Campaign::where('scheduled_at', '<=', now())
            ->where('status', 'scheduled')
            ->get();
        $this->info("🕒 Thời gian hiện tại: " . now());
        foreach ($campaigns as $campaign) {
            SendCampaignJob::dispatch($campaign);
            $this->info("Đã dispatch chiến dịch: {$campaign->name}");
        }
    }
}

