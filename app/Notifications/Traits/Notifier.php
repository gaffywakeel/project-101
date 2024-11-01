<?php

namespace App\Notifications\Traits;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use NotificationChannels\AwsSns\SnsMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;
use ReflectionClass;

trait Notifier
{
    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 1;

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = [];

        $preferred = $this->preferred($notifiable);

        if ($this->includeSms()) {
            $channels[] = getSmsChannel();
        }

        if ($this->includeMail()) {
            $channels[] = 'mail';
        }

        if ($this->includeDatabase()) {
            $channels[] = 'broadcast';
            $channels[] = 'database';
        }

        return array_intersect($channels, $preferred);
    }

    /**
     * Get preferred notification channel
     */
    public function preferred($notifiable): array
    {
        $channels = [];

        if ($notifiable instanceof User) {
            $name = $this->getNotificationName();

            $settings = $notifiable->getNotificationSettings()->firstWhere('name', $name);

            if ($settings?->sms) {
                $channels[] = getSmsChannel();
            }

            if ($settings?->email) {
                $channels[] = 'mail';
            }

            if ($settings?->database) {
                $channels[] = 'broadcast';
                $channels[] = 'database';
            }
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $name = $this->getNotificationName();
        $replacement = $this->replacements($notifiable);

        if (view()->exists("mail.$name")) {
            return (new MailMessage)
                ->subject(trans("notifications.$name.mail.subject", $replacement))
                ->view("mail.$name", $replacement);
        }

        return tap(new MailMessage, function (MailMessage $message) use ($name, $replacement) {
            if (trans()->has("notifications.$name.mail.greeting")) {
                $message->greeting(trans("notifications.$name.mail.greeting", $replacement));
            }

            $message->line(trans("notifications.$name.mail.content", $replacement));

            if ($url = Arr::get($replacement, 'actionUrl')) {
                $message->action(trans("notifications.$name.mail.action", $replacement), $url);
            }

            $message->subject(trans("notifications.$name.mail.subject", $replacement));
        });
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $name = $this->getNotificationName();
        $replacement = $this->replacements($notifiable);

        return ['content' => trans("notifications.$name.database.content", $replacement)];
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     */
    public function toVonage($notifiable): VonageMessage
    {
        $name = $this->getNotificationName();
        $replacement = $this->replacements($notifiable);

        return (new VonageMessage)
            ->content(trans("notifications.$name.sms.content", $replacement));
    }

    /**
     * Get the Aws SNS representation of the notification.
     */
    public function toSns($notifiable): SnsMessage
    {
        $name = $this->getNotificationName();
        $replacement = $this->replacements($notifiable);

        return SnsMessage::create()->promotional()
            ->body(trans("notifications.$name.sms.content", $replacement))
            ->sender(substr(config('app.name'), 0, 11));
    }

    /**
     * Get the Twilio representation of the notification.
     */
    public function toTwilio($notifiable): TwilioSmsMessage
    {
        $name = $this->getNotificationName();
        $replacement = $this->replacements($notifiable);

        return (new TwilioSmsMessage())
            ->content(trans("notifications.$name.sms.content", $replacement));
    }

    /**
     * Check the email channel status for this notification
     */
    protected function includeMail(): bool
    {
        return settings()->get('enable_mail') && !$this->disabledFor('mail');
    }

    /**
     * Check the database channel status for this notification
     */
    protected function includeDatabase(): bool
    {
        return settings()->get('enable_database') && !$this->disabledFor('database');
    }

    /**
     * Check the sms channel status for this notification
     */
    protected function includeSms(): bool
    {
        return settings()->get('enable_sms') && !$this->disabledFor('sms');
    }

    /**
     * Check if notification is disabled for certain channel
     */
    protected function disabledFor($channel): bool
    {
        return $this->{Str::camel("disable_$channel")} ?? false;
    }

    /**
     * Get replacements parameters
     */
    protected function replacements($notifiable): array
    {
        $parameters = !method_exists($this, 'parameters') ? [] : $this->parameters($notifiable);

        return array_merge([
            'application' => config('app.name'),
            'username' => $notifiable->name,
        ], $parameters);
    }

    /**
     * Get notification name
     */
    protected function getNotificationName(): string
    {
        return Str::snake((new ReflectionClass($this))->getShortName());
    }
}
