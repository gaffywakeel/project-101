<?php

namespace App\Models;

use App\Models\Support\Memoization;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StakeRate extends Model
{
    use HasFactory, Memoization;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'days',
        'rate',
        'min_value',
        'max_value',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'days' => 'float',
        'rate' => 'float',
        'min_value' => 'float',
        'max_value' => 'float',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['annual_rate'];

    /**
     * Calculate APY
     */
    protected function annualRate(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            return round(365 * $attributes['rate'] / $attributes['days'], 2);
        });
    }
}
