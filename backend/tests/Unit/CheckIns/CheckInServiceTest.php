<?php

namespace Tests\Unit\Services\CheckInService;

use App\Models\CheckInLog;
use App\Models\User;
use App\Repositories\CheckInEloquentRepository;
use App\Services\CheckIns\CheckInService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;
use Mockery;

class CheckInServiceTest extends TestCase
{
    protected $service;
    protected $repoMock;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->make(['id' => 1]);
        Auth::shouldReceive('id')->andReturn($this->user->id);

        $this->repoMock = Mockery::mock(CheckInEloquentRepository::class);
        $this->service = new CheckInService($this->repoMock);
    }

    public function test_check_in_successfully()
    {
        $this->repoMock->shouldReceive('getTodayLog')->andReturn(null);
        $this->repoMock->shouldReceive('createCheckIn')->andReturn(new CheckInLog(['user_id' => 1]));

        $result = $this->service->checkIn();

        $this->assertInstanceOf(CheckInLog::class, $result);
    }

    public function test_check_in_fails_if_already_checked_in()
    {
        $log = new CheckInLog(['check_in_time' => now()]);
        $this->repoMock->shouldReceive('getTodayLog')->andReturn($log);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Bạn đã checkin hôm nay rồi.');

        $this->service->checkIn();
    }

    public function test_check_out_fails_if_not_checked_in()
    {
        $this->repoMock->shouldReceive('getTodayLog')->andReturn(null);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Bạn phải checkin trước khi checkout.');

        $this->service->checkOut();
    }

    public function test_check_out_fails_if_already_checked_out()
    {
        $log = new CheckInLog([
            'check_in_time' => now()->subHours(3),
            'check_out_time' => now()->subHour(),
        ]);

        $this->repoMock->shouldReceive('getTodayLog')->andReturn($log);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Bạn đã checkout hôm nay rồi.');

        $this->service->checkOut();
    }

    public function test_get_today_log_returns_log()
    {
        $log = new CheckInLog(['check_in_time' => now()]);

        $this->repoMock->shouldReceive('getTodayLog')
            ->once()
            ->with($this->user->id)
            ->andReturn($log);

        $result = $this->service->getTodayLog();

        $this->assertEquals($log, $result);
    }

    public function test_get_check_in_logs_with_filters()
    {
        $requestMock = Mockery::mock(Request::class);

        $requestMock->shouldReceive('filled')->with('username')->andReturn(true);
        $requestMock->shouldReceive('input')->with('username')->andReturn('john');

        $requestMock->shouldReceive('filled')->with('date')->andReturn(true);
        $requestMock->shouldReceive('input')->with('date')->andReturn('2025-07-15');

        $requestMock->shouldReceive('filled')->with('status')->andReturn(true);
        $requestMock->shouldReceive('input')->with('status')->andReturn('checked-in');

        $requestMock->shouldReceive('filled')->with('per_page')->andReturn(true);
        $requestMock->shouldReceive('input')->with('per_page')->andReturn(25);

        $expectedFilters = [
            'username' => 'john',
            'date' => '2025-07-15',
            'status' => 'checked-in',
            'per_page' => 25,
        ];

        $this->repoMock->shouldReceive('getFilteredLogs')
            ->with($expectedFilters)
            ->once()
            ->andReturn(collect([]));

        $result = $this->service->getCheckInLogs($requestMock);
        $this->assertInstanceOf(Collection::class, $result);
    }
}
