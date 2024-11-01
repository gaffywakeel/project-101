<?php

namespace App\Notifications\Auth;

use App\Notifications\Traits\Notifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable, Notifier;

    /**
     * Get the notification's channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        $id = $notifiable->getKey();
        $hash = sha1($notifiable->getEmailForVerification());

        return URL::temporarySignedRoute('email-verification.verify', now()->addMinutes(60), compact('id', 'hash'));
    }

    /**
     * Replacement Parameters and Values
     */
    protected function parameters($notifiable): array
    {
        return ['actionUrl' => $this->verificationUrl($notifiable)];
    }
}
