<?php

namespace App\Jobs;

use App\Mail\Users\BirthdateMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBirthdayGreetingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        Mail::to($this->user->email)->send(new BirthdateMail($this->user));

        $this->user->update([
            'last_birthday_greeted_at' => now()->toDateString(),
        ]);
        Log::info("Đã gửi lời chúc mừng sinh nhật đến: {$this->user->email}");
    }
}
