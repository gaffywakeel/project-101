<?php

namespace App\Notifications;

use App\Models\TransferRecord;
use App\Notifications\Traits\Notifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WalletCredit extends Notification implements ShouldQueue
{
    use Queueable, Notifier;

    protected TransferRecord $transferRecord;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TransferRecord $transferRecord)
    {
        $this->transferRecord = $transferRecord;
    }

    /**
     * Replacement Parameters and Values
     */
    protected function parameters($notifiable): array
    {
        return [
            'coin' => $this->transferRecord->coin->name,
            'value' => $this->transferRecord->value->getValue(),
            'value_price' => $this->transferRecord->value_price,
            'formatted_value_price' => $this->transferRecord->formatted_value_price,
            'description' => $this->transferRecord->description,
        ];
    }
}
