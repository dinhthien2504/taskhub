<?php

namespace Tests\Unit\Auth;

use App\Http\Requests\Auth\ProfileRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_profile_request_passes_with_valid_data()
    {
        $data = [
            'name' => 'Nguyen Van A',
            'phone' => '0123456789',
            'avatar' => UploadedFile::fake()->image('avatar.jpg')->size(500),
        ];

        $validator = $this->validation($data);
        $this->assertTrue($validator->passes());
    }

    public function test_profile_request_fails_with_invalid_data()
    {
        $data = [
            'name' => 'A', // quá ngắn
            'phone' => str_repeat('1', 20), // quá dài
            'avatar' => UploadedFile::fake()->create('file.txt', 10, 'text/plain'), // sai định dạng
        ];

        $validator = $this->validation($data);
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('phone', $validator->errors()->toArray());
        $this->assertArrayHasKey('avatar', $validator->errors()->toArray());
    }
    public function validation($data)
    {
        $request = new ProfileRequest();
        return Validator::make($data, $request->rules());
    }

}