<?php

namespace App\Models\Support;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRatings
{
    /**
     * Ratings relation
     */
    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    /**
     * Average rating
     */
    public function averageRating(): mixed
    {
        return $this->ratings()->avg('value');
    }

    /**
     * Sum rating
     */
    public function sumRating(): mixed
    {
        return $this->ratings()->sum('value');
    }

    /**
     * Total rating
     */
    public function totalRating(): int
    {
        return $this->ratings()->count();
    }

    /**
     * Total users rated
     */
    public function usersRated(): int
    {
        return $this->ratings()->groupBy('user_id')->pluck('user_id')->count();
    }

    /**
     * Average rating
     */
    protected function getAverageRatingAttribute(): float
    {
        return (float) $this->averageRating();
    }

    /**
     * Sum rating
     */
    protected function getSumRatingAttribute(): int
    {
        return (int) $this->sumRating();
    }

    /**
     * Total rating
     */
    protected function getTotalRatingAttribute(): int
    {
        return $this->totalRating();
    }
}
