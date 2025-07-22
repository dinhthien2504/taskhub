<?php

namespace Tests\Unit\Logs;

use App\Models\User;
use App\Models\UserActionLog;

use App\Repositories\UserActivityLogEloquentRepository;
use App\Services\Logs\userActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class LogServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_logs_returns_logs_with_filters()
    {
        $repo = Mockery::mock(UserActivityLogEloquentRepository::class);
        $service = new userActivityLogService(userActionLogRepository: $repo);
        $expected = collect([UserActionLog::factory()->make()]);
        $repo->shouldReceive('getAllLogs')
            ->with(['user'], 'search', 5, 6, 2025)
            ->once()
            ->andReturn($expected);
        $result = $service->getAllLogs('search', 5, 6, 2025);
        $this->assertEquals($expected, $result);
    }
}
