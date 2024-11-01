<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['data', 'status'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['data'];

    /**
     * Requirement
     */
    public function requirement(): BelongsTo
    {
        return $this->belongsTo(RequiredDocument::class, 'required_document_id', 'id');
    }

    /**
     * Encrypt data
     */
    protected function setDataAttribute(array $value): void
    {
        $this->attributes['data'] = encrypt($value);
    }

    /**
     * Decrypt data
     */
    protected function getDataAttribute(string $value): array
    {
        return decrypt($value);
    }

    /**
     * Related user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
