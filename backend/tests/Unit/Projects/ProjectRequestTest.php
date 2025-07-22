<?php

namespace Tests\Unit\Projects;

use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data, string $formRequestClass)
    {
        $request = new $formRequestClass();
        return Validator::make($data, $request->rules(), $request->messages(), $request->attributes());
    }

    // --- STORE PROJECT ---
    public function test_store_project_requires_valid_fields()
    {
        $data = [];
        $validator = $this->validate($data, StoreProjectRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
        $this->assertArrayHasKey('owner_id', $validator->errors()->messages());
    }

    public function test_store_project_fails_with_short_name()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'abc',
            'owner_id' => $user->id,
        ];
        $validator = $this->validate($data, StoreProjectRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    public function test_store_project_passes_with_valid_data()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Dự án hợp lệ',
            'owner_id' => $user->id,
            'description' => 'Mô tả ngắn'
        ];
        $validator = $this->validate($data, StoreProjectRequest::class);
        $this->assertFalse($validator->fails());
    }

    // --- UPDATE PROJECT ---
    public function test_update_project_requires_valid_name()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'abc',
            'owner_id' => $user->id,
        ];
        $validator = $this->validate($data, UpdateProjectRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    public function test_update_project_passes_with_valid_name()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Tên dự án mới',
            'owner_id' => $user->id,
        ];
        $validator = $this->validate($data, UpdateProjectRequest::class);
        $this->assertFalse($validator->fails());
    }
}