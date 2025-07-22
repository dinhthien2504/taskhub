<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Permission;


class UserImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected array $importExportPermissions = [
        'download users csv template',
        'export users csv',
        'import users from csv',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        foreach ($this->importExportPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    }

    public function test_can_download_csv_template()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('download users csv template');
        $this->actingAs($user);
        $response = $this->get('/api/users/csv-template');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $this->assertStringContainsString('name,email,phone', $content);
    }

    public function test_can_export_users_to_csv()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('export users csv');
        $this->actingAs($user);
        User::factory()->count(3)->create();
        $response = $this->get('/api/users/export-csv');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $this->assertStringContainsString('id,name,email,created_at', $content);
    }

    public function test_import_users_from_csv_success()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $user->givePermissionTo('import users from csv');
        $this->actingAs($user);
        $csv = "name,email,phone\nNguyen Van A,nguyena@example.com,0123456789\nNguyen Van B,nguyenb@example.com,0987654321\n";
        $file = UploadedFile::fake()->createWithContent('users.csv', $csv);
        $response = $this->post('/api/users/import-csv', [
            'file' => $file,
        ]);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Đang xử lý file. Vui lòng chờ trong giây lát.']);
    }

    public function test_import_users_from_csv_with_invalid_data()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $user->givePermissionTo('import users from csv');
        $this->actingAs($user);
        $csv = "name,email,phone\n,invalidemail,\n";
        $file = UploadedFile::fake()->createWithContent('users.csv', $csv);
        $response = $this->post('/api/users/import-csv', [
            'file' => $file,
        ]);
        $response->assertStatus(200);
        // Vì xử lý qua queue nên chỉ check message
        $response->assertJson(['message' => 'Đang xử lý file. Vui lòng chờ trong giây lát.']);
    }
}
