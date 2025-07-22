<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailCustom extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Xác thực email của bạn')
            ->line('Nhấn nút bên dưới để xác thực email.')
            ->action('Xác thực Email', $verificationUrl);
    }

    protected function verificationUrl($notifiable)
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
        $queryString = parse_url($signedUrl, PHP_URL_QUERY);

        $id = $notifiable->getKey();
        $hash = sha1($notifiable->getEmailForVerification());

        $frontend = config('app.frontend_url');

        return $frontend
            . '/verify-email'
            . "?id={$id}&hash={$hash}&{$queryString}";
    }

}
