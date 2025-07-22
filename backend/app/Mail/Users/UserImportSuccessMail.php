<?php
namespace App\Mail\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserImportSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Import Người Dùng Thành Công')
            ->view('emails.users.import_success');
    }
}
