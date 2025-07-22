<?php

namespace Tests\Unit\Services;

use App\Repositories\WorkingTimeEloquentRepository;
use App\Services\WorkingTimes\WorkingTimeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class WorkingTimeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repoMock = Mockery::mock(WorkingTimeEloquentRepository::class);
        $this->service = new WorkingTimeService($this->repoMock);
    }

    public function test_get_working_time_returns_data()
    {
        $mockData = [
            ['start_time' => '08:00', 'late_after' => '08:15', 'end_time' => '17:00'],
        ];

        $this->repoMock->shouldReceive('getWorkingTime')
            ->once()
            ->andReturn($mockData);

        $result = $this->service->getWorkingTime();

        $this->assertEquals($mockData, $result);
    }

    public function test_upsert_passes_only_filled_fields()
    {
        $requestMock = Mockery::mock(Request::class);

        $requestMock->shouldReceive('filled')->with('start_time')->andReturn(true);
        $requestMock->shouldReceive('input')->with('start_time')->andReturn('08:00');

        $requestMock->shouldReceive('filled')->with('late_after')->andReturn(true);
        $requestMock->shouldReceive('input')->with('late_after')->andReturn('08:15');

        $requestMock->shouldReceive('filled')->with('end_time')->andReturn(false);

        $expectedData = [
            'start_time' => '08:00',
            'late_after' => '08:15',
        ];

        $this->repoMock->shouldReceive('upsert')
            ->with($expectedData)
            ->once()
            ->andReturn($expectedData);

        $result = $this->service->upsert($requestMock);
        $this->assertEquals($expectedData, $result);
    }
}
