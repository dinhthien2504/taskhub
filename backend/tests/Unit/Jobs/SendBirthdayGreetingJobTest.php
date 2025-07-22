<?php
namespace Tests\Unit\Jobs;

use App\Jobs\SendBirthdayGreetingJob;
use App\Mail\Users\BirthdateMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendBirthdayGreetingJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_email_and_updates_user()
    {
        Mail::fake();

        $user = User::factory()->create(['last_birthday_greeted_at' => null]);

        $job = new SendBirthdayGreetingJob($user);
        $job->handle();

        Mail::assertSent(BirthdateMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $this->assertEquals(now()->toDateString(), $user->fresh()->last_birthday_greeted_at);
    }
}
