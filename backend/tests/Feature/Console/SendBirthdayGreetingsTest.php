<?php
namespace Tests\Feature\Console;

use App\Jobs\SendBirthdayGreetingJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendBirthdayGreetingsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        if (DB::getDriverName() === 'sqlite') {
            DB::getPdo()->sqliteCreateFunction('DATE_FORMAT', function ($date, $format) {
                return date($this->convertDateFormat($format), strtotime($date));
            });
        }
    }

    private function convertDateFormat($format)
    {
        return strtr($format, [
            '%Y' => 'Y',
            '%m' => 'm',
            '%d' => 'd',
        ]);

    }
    public function test_dispatches_job_for_users_with_birthday_today()
    {
        Queue::fake();

        $user = User::factory()->create([
            'birthdate' => now()->format('Y-m-d'),
            'last_birthday_greeted_at' => null,
            'is_opt_out' => false,
        ]);

        $this->artisan('birthdate:send')->assertExitCode(0);

        Queue::assertPushed(SendBirthdayGreetingJob::class, function ($job) use ($user) {
            return $job->user->id === $user->id;
        });
    }

    public function test_does_not_dispatch_for_opted_out_users()
    {
        Queue::fake();

        $user = User::factory()->create([
            'birthdate' => now()->format('Y-m-d'),
            'is_opt_out' => true,
        ]);

        $this->artisan('birthdate:send');

        Queue::assertNotPushed(SendBirthdayGreetingJob::class);
    }
}
