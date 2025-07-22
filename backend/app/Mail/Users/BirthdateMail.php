<?php

namespace App\Mail\Users;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BirthdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Chúc mừng sinh nhật bạn!')
            ->markdown('emails.users.birthdate')
            ->with([
                'name' => $this->user->name,
                'userId' => $this->user->id,
            ]);
    }
}
