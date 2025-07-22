<?php

namespace Tests\Unit\EmailTemplates;

use App\Models\EmailTemplate;
use App\Models\User;
use App\Repositories\EmailTemplateEloquentRepository;
use App\Services\EmailTemplates\EmailTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmailTemplateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $repo = new EmailTemplateEloquentRepository();
        $this->service = new EmailTemplateService($repo);
    }

    public function test_create_email_template_success()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $data = [
            'name' => 'Mẫu email',
            'subject' => 'Tiêu đề',
            'content' => 'Nội dung',
            'description' => 'Mô tả',
            'type' => 'default',
            'is_active' => true,
        ];
        $template = $this->service->create($data);
        $this->assertEquals('Mẫu email', $template->name);
        $this->assertDatabaseHas('email_templates', ['name' => 'Mẫu email']);
    }

    public function test_update_email_template_success()
    {
        $template = EmailTemplate::factory()->create(['name' => 'Cũ', 'subject' => 'Cũ', 'content' => 'Cũ']);
        $data = [
            'name' => 'Mới',
            'subject' => 'Tiêu đề mới',
            'content' => 'Nội dung mới',
            'description' => 'Mô tả mới',
            'type' => 'default',
            'is_active' => false,
        ];
        $updated = $this->service->update($template->id, $data);
        $this->assertEquals('Mới', $updated->name);
        $this->assertEquals('Tiêu đề mới', $updated->subject);
    }

    public function test_delete_email_template_success()
    {
        $template = EmailTemplate::factory()->create();
        $this->service->delete($template->id);
        $this->assertSoftDeleted('email_templates', ['id' => $template->id]);
    }

    public function test_restore_email_template_success()
    {
        $template = EmailTemplate::factory()->create();
        $template->delete();
        $restored = $this->service->restore($template->id);
        $this->assertEquals($template->id, $restored['id']);
        $this->assertDatabaseHas('email_templates', ['id' => $template->id, 'deleted_at' => null]);
    }
}
