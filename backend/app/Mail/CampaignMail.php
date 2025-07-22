<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Services\EmailTemplates\ShortcodeParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CampaignMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Campaign $campaign, public array $shortcodeData = [])
    {
    }

    public function build()
    {
        $template = $this->campaign->template;

        if (!$template) {
            throw new \Exception("Template cho chiến dịch '{$this->campaign->name}' không tồn tại hoặc đã bị xóa.");
        }

        $parsedContent = ShortcodeParserService::parse($template->content, $this->shortcodeData);

        return $this->subject('Chiến dịch: ' . $this->campaign->name)
                    ->html($parsedContent);
    }
}
