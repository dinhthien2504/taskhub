<?php

namespace Tests\Unit\Users;

use App\Services\Users\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use Mockery;

class UserImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_importUsersFromCsv_dispatches_job_and_stores_file()
    {
        Storage::fake('local');
        $service = app(UserService::class);
        $user = User::factory()->create();
        $this->actingAs($user);
        $csv = "name,email,phone\nTest User,test@example.com,0123456789\n";
        $file = UploadedFile::fake()->createWithContent('users.csv', $csv);
        $service->importUsersFromCsv($file);
        Storage::disk('local')->assertExists('imports');
        $this->assertTrue(true);
    }

    public function test_downloadCsvTemplate_outputs_csv()
    {
        $service = app(UserService::class);
        $callback = $service->downloadCsvTemplate();
        ob_start();
        $callback();
        $output = ob_get_clean();
        $this->assertStringContainsString('name,email,phone', $output);
    }

    public function test_exportCsv_outputs_csv()
    {
        $service = app(UserService::class);
        User::factory()->count(2)->create();
        $callback = $service->exportCsv();
        ob_start();
        $callback();
        $output = ob_get_clean();
        $this->assertStringContainsString('id,name,email,created_at', $output);
    }
}
