<?php

namespace Tests\Unit\Campaigns;

use App\Http\Requests\Campaigns\StoreCampaignRequest;
use App\Http\Requests\Campaigns\UpdateCampaignRequest;
use App\Http\Requests\Campaigns\AssignUsersToCampaignRequest;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data, string $formRequestClass)
    {
        $request = new $formRequestClass();
        return Validator::make($data, $request->rules(), $request->messages(), $request->attributes());
    }

    // --- STORE CAMPAIGN ---
    public function test_store_campaign_requires_valid_fields()
    {
        $data = [];
        $validator = $this->validate($data, StoreCampaignRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
        $this->assertArrayHasKey('start_date', $validator->errors()->messages());
        $this->assertArrayHasKey('end_date', $validator->errors()->messages());
        $this->assertArrayHasKey('status', $validator->errors()->messages());
    }

    public function test_store_campaign_fails_with_invalid_status()
    {
        $data = [
            'name' => 'Test',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(1)->toDateString(),
            'status' => 'invalid',
        ];
        $validator = $this->validate($data, StoreCampaignRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->messages());
    }

    public function test_store_campaign_passes_with_valid_data()
    {
        $template = EmailTemplate::factory()->create();
        $data = [
            'name' => 'Chiến dịch hợp lệ',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(1)->toDateString(),
            'status' => 'pending',
            'template_id' => $template->id
        ];
        $validator = $this->validate($data, StoreCampaignRequest::class);
        $this->assertFalse($validator->fails());
    }

    // --- UPDATE CAMPAIGN ---
    public function test_update_campaign_requires_valid_fields()
    {
        $data = [];
        $validator = $this->validate($data, UpdateCampaignRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
        $this->assertArrayHasKey('start_date', $validator->errors()->messages());
        $this->assertArrayHasKey('end_date', $validator->errors()->messages());
        $this->assertArrayHasKey('status', $validator->errors()->messages());
    }

    public function test_update_campaign_passes_with_valid_data()
    {
        $template = EmailTemplate::factory()->create();
        $data = [
            'name' => 'Chiến dịch update',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(1)->toDateString(),
            'status' => 'pending',
            'template_id' => $template->id
        ];
        $validator = $this->validate($data, UpdateCampaignRequest::class);
        $this->assertFalse($validator->fails());
    }
}
