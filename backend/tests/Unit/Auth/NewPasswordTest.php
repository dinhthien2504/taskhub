<?php

namespace Tests\Unit\Auth;

use App\Http\Requests\Auth\NewPasswordRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class NewPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data)
    {
        $request = new NewPasswordRequest();
        return Validator::make($data, $request->rules(), $request->messages(), $request->attributes());
    }

    public function test_missing_required_fields_fails_validation()
    {
        $validator = $this->validate([]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('token', $validator->errors()->messages());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
        $this->assertArrayHasKey('password_confirmation', $validator->errors()->messages());
    }

    public function test_invalid_email_format_fails_validation()
    {
        $data = [
            'token' => 'sometoken',
            'email' => 'invalid-email',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ];
        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
    }

    public function test_password_confirmation_mismatch_fails_validation()
    {
        $data = [
            'token' => 'sometoken',
            'email' => 'user@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password2!',
        ];
        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
    }

    public function test_password_format_fails_validation()
    {
        $data = [
            'token' => 'sometoken',
            'email' => 'user@example.com',
            'password' => 'password', // Không đủ mạnh
            'password_confirmation' => 'password',
        ];
        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
    }

    public function test_valid_data_passes_validation()
    {
        $data = [
            'token' => 'sometoken',
            'email' => 'user@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ];
        $validator = $this->validate($data);

        $this->assertFalse($validator->fails());
    }
}