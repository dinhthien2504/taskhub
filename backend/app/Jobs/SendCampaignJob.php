<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\Campaign;
use App\Services\EmailTemplates\ShortcodeParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle()
    {
        try {
            $campaign = $this->campaign->load(['template', 'users']);
            $template = $campaign->template;

            if (!$template) {
                throw new \Exception("Không tìm thấy template cho chiến dịch");
            }

            //Lấy ra các shortcodes
            $shortcodes = ShortcodeParserService::extract($template->content);
            foreach ($campaign->users as $user) {
                $data = [];

                $templateData = [
                        'user' => $user,
                        'campaign' => $campaign,
                    ];
                foreach ($shortcodes as $key) {
                     $data[$key] = data_get($templateData, $key, '');
                }
                 Mail::to($user->email)->send(new CampaignMail($campaign, $data));
            }

            $campaign->update(['status' => 'sent']);
        } catch (\Exception $e) {
            $this->campaign->update(['status' => 'failed']);
            throw $e;
        }
    }
}