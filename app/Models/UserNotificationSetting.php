<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'database',
        'sms',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email' => 'boolean',
        'database' => 'boolean',
        'sms' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['title'];

    /**
     * Get title attribute
     */
    protected function getTitleAttribute(): string
    {
        return trans("notifications.$this->name.title");
    }

    /**
     * Check if channel is disabled
     */
    public function isDisabled(string $channel): bool
    {
        $config = config("notifications.settings.$this->name");

        return is_null(data_get($config, $channel));
    }
}
