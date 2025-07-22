<?php

namespace Tests\Feature\EmailTemplates;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        Permission::firstOrCreate(['name' => 'view email templates']);
        Permission::firstOrCreate(['name' => 'create email templates']);
        Permission::firstOrCreate(['name' => 'update email templates']);
        Permission::firstOrCreate(['name' => 'delete email templates']);
        Permission::firstOrCreate(['name' => 'restore email templates']);
        Permission::firstOrCreate(['name' => 'view trashed email templates']);

        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo([
            'view email templates',
            'create email templates',
            'update email templates',
            'delete email templates',
            'restore email templates',
            'view trashed email templates',
        ]);
        $this->admin->assignRole('admin');
        $this->actingAs($this->admin);
    }

    public function test_can_list_email_templates()
    {
        EmailTemplate::factory()->count(2)->create();
        $response = $this->getJson('/api/email-templates');
        $response->assertOk();
        $this->assertGreaterThanOrEqual(2, count($response->json('data')));
    }

    public function test_can_create_email_template()
    {
        $payload = [
            'name' => 'Mẫu email',
            'subject' => 'Tiêu đề',
            'content' => 'Nội dung',
            'description' => 'Mô tả',
            'type' => 'default',
            'is_active' => true,
        ];
        $response = $this->postJson('/api/email-templates', $payload);
        $response->assertCreated();
        $this->assertDatabaseHas('email_templates', ['name' => 'Mẫu email']);
    }

    public function test_can_update_email_template()
    {
        $template = EmailTemplate::factory()->create();
        $payload = [
            'name' => 'Mẫu email update',
            'subject' => 'Tiêu đề update',
            'content' => 'Nội dung update',
            'description' => 'Mô tả update',
            'type' => 'default',
            'is_active' => false,
        ];
        $response = $this->putJson('/api/email-templates/' . $template->id, $payload);
        $response->assertOk();
        $this->assertDatabaseHas('email_templates', ['id' => $template->id, 'name' => 'Mẫu email update']);
    }

    public function test_can_delete_email_template()
    {
        $template = EmailTemplate::factory()->create();
        $response = $this->deleteJson('/api/email-templates/' . $template->id);
        $response->assertOk();
        $this->assertSoftDeleted('email_templates', ['id' => $template->id]);
    }

    public function test_can_restore_email_template()
    {
        $template = EmailTemplate::factory()->create();
        $template->delete();
        $response = $this->putJson('/api/email-templates/' . $template->id . '/restore');
        $response->assertOk();
        $this->assertDatabaseHas('email_templates', ['id' => $template->id, 'deleted_at' => null]);
    }

    public function test_can_view_trashed_email_templates()
    {
        $template = EmailTemplate::factory()->create();
        $template->delete();
        $response = $this->getJson('/api/email-templates/trashed');
        $response->assertOk();
        $this->assertTrue(collect($response->json())->contains('id', $template->id));
    }
}