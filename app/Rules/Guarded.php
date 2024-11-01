<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Guarded implements ValidationRule
{
    /**
     * Expected field value
     */
    protected mixed $value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_null($this->value) && $value !== $this->value) {
            $fail('validation.protected')->translate();
        }
    }
}
