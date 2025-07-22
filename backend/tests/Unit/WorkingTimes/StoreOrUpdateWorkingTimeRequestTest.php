<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\WorkingTimes\StoreOrUpdateWorkingTimeRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreOrUpdateWorkingTimeRequestTest extends TestCase
{
    public function test_validation_passes_with_valid_data()
    {
        $data = [
            'start_time' => '08:00',
            'late_after' => '08:15',
            'end_time' => '17:00',
        ];

        $request = new StoreOrUpdateWorkingTimeRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->passes());
    }

    public function test_validation_fails_when_late_after_is_before_start_time()
    {
        $data = [
            'start_time' => '08:00',
            'late_after' => '07:30',
        ];

        $request = new StoreOrUpdateWorkingTimeRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('late_after', $validator->errors()->toArray());
    }

    public function test_validation_fails_when_end_time_is_before_start_time()
    {
        $data = [
            'start_time' => '09:00',
            'late_after' => '09:15',
            'end_time' => '08:30',
        ];

        $request = new StoreOrUpdateWorkingTimeRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('end_time', $validator->errors()->toArray());
    }
}
