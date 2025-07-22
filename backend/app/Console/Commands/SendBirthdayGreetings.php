<?php

namespace App\Console\Commands;

use App\Jobs\SendBirthdayGreetingJob;
use App\Models\User;
use Illuminate\Console\Command;

class SendBirthdayGreetings extends Command
{
    protected $signature = 'birthdate:send';

    protected $description = 'Gửi lời chúc mừng đến người dùng.';

    public function handle()
    {
        //Lấy thời gian hiện tại
        $today = now()->format('m-d');

        //Lấy users có sinh nhật trong thời gian hiện tại
        $users = User::whereNotNull('birthdate')
            ->whereRaw("DATE_FORMAT(birthdate, '%m-%d') = ?", [$today])
            ->where(function ($query) {
                $query->whereNull('last_birthday_greeted_at')
                    ->orWhereDate('last_birthday_greeted_at', '<>', now()->toDateString());
            })
            ->get();

        foreach ($users as $user) {
            if (!$user->is_opt_out) {
                SendBirthdayGreetingJob::dispatch($user);
            }
        }

        return 0;
    }
}
