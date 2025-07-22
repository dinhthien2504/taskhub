<?php

namespace Tests\Unit\Logs;

use App\Http\Requests\Logs\UserActionLogRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_rules()
    {
        $request = new UserActionLogRequest();
        $rules = $request->rules();
        $this->assertArrayHasKey('search', $rules);
        $this->assertArrayHasKey('per_page', $rules);
        $this->assertArrayHasKey('month', $rules);
        $this->assertArrayHasKey('year', $rules);
    }

}
