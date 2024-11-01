<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class Username implements ValidationRule
{
    /**
     * Censored usernames
     *
     * @var string[]
     */
    protected array $censored;

    /**
     * Initialize censored array
     */
    public function __construct()
    {
        $this->censored = ['admin'];
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^[A-z0-9_\-.]+$/', $value)) {
            $fail('validation.username')->translate();
        }

        if (Str::contains($value, $this->censored, true)) {
            $fail('validation.censored')->translate();
        }
    }
}
