<?php
namespace App\Jobs;

use App\Mail\Users\UserImportFailedReportMail;
use App\Mail\Users\UserImportSuccessMail;
use App\Repositories\UserEloquentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected string $userEmail;

    public function __construct(string $filePath, string $userEmail)
    {
        $this->filePath = $filePath;
        $this->userEmail = $userEmail;

    }

    public function handle(UserEloquentRepository $userRepository)
    {
        $stream = Storage::readStream($this->filePath);

        $header = fgetcsv($stream);
        $header = array_map(fn($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', $h)), $header);

        $rowNumber = 1;
        $failedRows = [];
        while (($row = fgetcsv($stream)) !== false) {
            $rowNumber++;

            if (empty(array_filter($row))) {
                continue;
            }

            $data = array_combine($header, $row);
            if (!$data) {
                continue;
            }

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                $failedRows[] = [
                    'row' => $rowNumber,
                    'data' => $data,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            $userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => bcrypt('password'),
            ]);
        }

        fclose($stream);
        \Log::info("Gửi mail tới: " . $this->userEmail);
        if (!empty($failedRows)) {
            Mail::to($this->userEmail)->send(new UserImportFailedReportMail($failedRows));
        } else {
            Mail::to($this->userEmail)->send(new UserImportSuccessMail());
        }
    }
}
