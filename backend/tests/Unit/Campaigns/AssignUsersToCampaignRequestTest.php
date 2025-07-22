<?php

namespace Tests\Unit\Campaigns;

use App\Http\Requests\Campaigns\AssignUsersToCampaignRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignUsersToCampaignRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data)
    {
        $request = new AssignUsersToCampaignRequest();
        return Validator::make($data, $request->rules(), $request->messages(), $request->attributes());
    }

    public function test_user_ids_is_required_and_array()
    {
        $validator = $this->validate([]);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_ids', $validator->errors()->messages());
    }

    public function test_user_ids_must_exist()
    {
        $validator = $this->validate(['user_ids' => [999]]);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_ids.0', $validator->errors()->messages());
    }

    public function test_passes_with_valid_user_ids()
    {
        $users = User::factory()->count(2)->create();
        $ids = $users->pluck('id')->toArray();
        $validator = $this->validate(['user_ids' => $ids]);
        $this->assertFalse($validator->fails());
    }
}
