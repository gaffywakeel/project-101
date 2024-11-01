<?php

namespace App\Notifications\Auth;

use App\Models\User;
use App\Notifications\Traits\Notifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EmailToken extends Notification implements ShouldQueue
{
    use Queueable, Notifier;

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return settings()->get('enable_mail') ? ['mail'] : [];
    }

    /**
     * Replacement Parameters and Values
     */
    public function parameters($notifiable): array
    {
        if ($notifiable instanceof User) {
            return $notifiable->generateEmailToken();
        }

        return [];
    }
}
