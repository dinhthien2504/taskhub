<?php
namespace App\Mail\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserImportFailedReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $failedRows;

    public function __construct(array $failedRows)
    {
        $this->failedRows = $failedRows;
    }

    public function build()
    {
        return $this->subject('Báo Cáo Import Người Dùng Thất Bại')
            ->view('emails.users.import_failed')
            ->with([
                'failedRows' => $this->failedRows,
            ]);
    }
}
