<?php

namespace App\Notifications\Auth;

use App\Notifications\Traits\Notifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PhoneToken extends Notification implements ShouldQueue
{
    use Queueable, Notifier;

    /**
     * Get the notification's channels.
     */
    public function via($notifiable): array
    {
        return settings()->get('enable_sms') ? [getSmsChannel()] : [];
    }

    /**
     * Replacement Parameters and Values
     */
    protected function parameters($notifiable): array
    {
        return $notifiable->generatePhoneToken();
    }
}
