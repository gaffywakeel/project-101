<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class Token
{
    /**
     * Get token key
     */
    private function getKey(string $identifier): string
    {
        return "token:$identifier";
    }

    /**
     * Validate token
     */
    public function validate(string $identifier, string $token): bool
    {
        return Cache::get($this->getKey($identifier)) === $token;
    }

    /**
     * Generate token
     */
    public function generate(string $identifier, int $digits = 6, int $duration = 1): array
    {
        $expires_at = now()->addMinutes($duration);
        $key = $this->getKey($identifier);

        $token = Cache::get($key, $this->generateToken($digits));

        if ($token && !Cache::has($key)) {
            Cache::put($key, $token, $expires_at);
        }

        return compact('token', 'expires_at', 'duration');
    }

    /**
     * Generate token
     */
    private function generateToken(int $digits = 6): string
    {
        $pin = '';

        for ($i = 0; $i < $digits; $i++) {
            $pin .= mt_rand(0, 9);
        }

        return $pin;
    }
}
