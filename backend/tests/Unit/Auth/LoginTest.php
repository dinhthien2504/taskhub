<?php

namespace Tests\Unit\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_email_format_fails_validation()
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'Password1!', 
        ];

        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
    }

    public function test_missing_email_fails_validation()
    {
        $data = [
            'password' => 'Password1!',
        ];

        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
    }

    public function test_missing_password_fails_validation()
    {
        $data = [
            'email' => 'user@example.com',
        ];

        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
    }

    public function test_valid_data_passes_validation()
    {
        $data = [
            'email' => 'user@example.com',
            'password' => 'Password1!',
        ];

        $validator = $this->validate($data);

        $this->assertFalse($validator->fails());
    }

    private function validate(array $data)
    {
        $request = new LoginRequest();
        return Validator::make($data, $request->rules());
    }
}