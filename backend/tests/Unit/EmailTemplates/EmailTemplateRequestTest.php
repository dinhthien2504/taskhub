<?php

namespace Tests\Unit\EmailTemplates;

use App\Http\Requests\EmailTemplates\StoreEmailTemplateRequest;
use App\Http\Requests\EmailTemplates\UpdateEmailTemplateRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data, string $formRequestClass)
    {
        $request = new $formRequestClass();
        return Validator::make($data, $request->rules(), $request->messages(), $request->attributes());
    }

    public function test_store_email_template_requires_valid_fields()
    {
        $data = [];
        $validator = $this->validate($data, StoreEmailTemplateRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
        $this->assertArrayHasKey('subject', $validator->errors()->messages());
        $this->assertArrayHasKey('content', $validator->errors()->messages());
    }

    public function test_store_email_template_passes_with_valid_data()
    {
        $data = [
            'name' => 'Mẫu email',
            'subject' => 'Tiêu đề',
            'content' => 'Nội dung',
            'description' => 'Mô tả',
            'type' => 'default',
            'is_active' => true,
        ];
        $validator = $this->validate($data, StoreEmailTemplateRequest::class);
        $this->assertFalse($validator->fails());
    }

    public function test_update_email_template_requires_valid_fields()
    {
        $data = [
            'name' => null,
            'subject' => 'Tiêu đề',
            'content' => 'Nội dung'
        ];
        $validator = $this->validate($data, UpdateEmailTemplateRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());

        $data = [
            'name' => 'Tên',
            'subject' => null,
            'content' => 'Nội dung'
        ];
        $validator = $this->validate($data, UpdateEmailTemplateRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('subject', $validator->errors()->messages());

        $data = [
            'name' => 'Tên',
            'subject' => 'Tiêu đề',
            'content' => null
        ];
        $validator = $this->validate($data, UpdateEmailTemplateRequest::class);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('content', $validator->errors()->messages());
    }

    public function test_update_email_template_passes_with_valid_data()
    {
        $data = [
            'name' => 'Mẫu email update',
            'subject' => 'Tiêu đề update',
            'content' => 'Nội dung update',
            'description' => 'Mô tả update',
            'type' => 'default',
            'is_active' => false,
        ];
        $validator = $this->validate($data, UpdateEmailTemplateRequest::class);
        $this->assertFalse($validator->fails());
    }
}