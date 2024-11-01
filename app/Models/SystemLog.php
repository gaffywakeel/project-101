<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'seen_at' => 'datetime',
    ];

    /**
     * Mark the log as seen.
     */
    public function markAsSeen(): void
    {
        if (is_null($this->seen_at)) {
            $this->update(['seen_at' => $this->freshTimestamp()]);
        }
    }

    /**
     * Unseen scope
     */
    public function scopeUnseen(Builder $query): Builder
    {
        return $query->whereNull('seen_at');
    }

    /**
     * Seen scope
     */
    public function scopeSeen(Builder $query): Builder
    {
        return $query->whereNotNull('seen_at');
    }

    /**
     * Info log
     */
    public static function info(string $message): SystemLog
    {
        return self::create([
            'message' => $message,
            'level' => 'info',
        ]);
    }

    /**
     * Warning log
     */
    public static function warning(string $message): SystemLog
    {
        return self::create([
            'message' => $message,
            'level' => 'warning',
        ]);
    }

    /**
     * Error log
     */
    public static function error(string $message): SystemLog
    {
        return self::create([
            'message' => $message,
            'level' => 'error',
        ]);
    }
}
