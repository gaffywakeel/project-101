<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidateRecaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     *
     * @throws GuzzleException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $client = new Client([
            'base_uri' => 'https://hcaptcha.com/',
        ]);

        $response = $client->post('siteverify', [
            'query' => [
                'secret' => config('services.recaptcha.secret'),
                'response' => $value,
            ],
        ]);

        if (!json_decode($response->getBody())->success) {
            $fail('validation.recaptcha')->translate();
        }
    }
}
