<?php

namespace App\Rules;

use Brick\Math\BigDecimal;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Decimal implements ValidationRule
{
    /**
     * Max precision
     */
    protected int $maxPrecision = 36;

    /**
     * Max scale
     */
    protected int $maxScale = 18;

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value)) {
            $fail('validation.numeric')->translate();
        } else {
            $decimal = BigDecimal::of($value)->abs();
            $precision = strlen((string) $decimal->getUnscaledValue());

            if ($this->maxPrecision && $precision > $this->maxPrecision) {
                $fail('validation.decimal_max_precision')->translate([
                    'max' => $this->maxPrecision,
                ]);
            }

            if ($this->maxScale && $decimal->getScale() > $this->maxScale) {
                $fail('validation.decimal_max_scale')->translate([
                    'max' => $this->maxScale,
                ]);
            }
        }
    }

    /**
     * Set max precision
     */
    public function maxPrecision(int $maxPrecision): static
    {
        $this->maxPrecision = $maxPrecision;

        return $this;
    }

    /**
     * Set max scale
     */
    public function maxScale(int $maxScale): static
    {
        $this->maxScale = $maxScale;

        return $this;
    }

    /**
     * Return new instance
     */
    public static function instance(): Decimal
    {
        return new self;
    }
}
