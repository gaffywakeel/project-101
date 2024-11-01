<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class TwoFactorToken implements ValidationRule
{
    /**
     * User resource to validate
     */
    protected ?User $user;

    /**
     * Construct TwoFactorToken rule
     */
    public function __construct(User $user = null)
    {
        $this->user = $user ?: Auth::user();
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->user?->verifyTwoFactorToken($value)) {
            $fail('auth.invalid_token')->translate();
        }
    }
}
