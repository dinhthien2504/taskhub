<?php
namespace Tests\Unit\Auth;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase; //Đảm bảo database được reset về bạn đầu trước mỗi test


    public function test_invalid_email_format_fails_validation()
    {
        $data = [
            'name' => 'Nguyễn Văn A',
            'email' => 'invalid-email', //Email false
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ];

        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
    }

    public function test_email_already_exists_in_database()
    {
        User::factory()->create([
            'email' => 'user@example.com'
        ]);
        $data = [
            'name' => 'Nguyễn Văn A',
            'email' => 'user@example.com', //email exists
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ];

        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->messages());


    }

    public function test_password_format_fails_validation()
    {
        $data = [
            'name' => 'Nguyễn Văn A',
            'email' => 'user@example.com',
            'password' => 'password_fails', //True => @Password1
            'password_confirmation' => 'password_fails',
        ];

        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
    }

    public function test_missing_required_fields_fails_validation()
    {
        $data = [];
        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
    }

    public function test_password_confirmation_mismatch_fails_validation()
    {
        $data = [
            'name' => 'Nguyễn Văn A',
            'email' => 'user2@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password2!', // Không khớp
        ];
        $validator = $this->validate($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
    }

    public function test_valid_data_passes_validation()
    {
        $data = [
            'name' => 'Nguyễn Văn A',
            'email' => 'valid@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ];
        $validator = $this->validate($data);

        $this->assertFalse($validator->fails());
    }

    private function validate(array $data)
    {
        $request = new RegisterRequest();
        return Validator::make($data, $request->rules());
    }

    
}
