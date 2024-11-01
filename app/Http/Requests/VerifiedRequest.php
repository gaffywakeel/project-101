<?php

namespace App\Http\Requests;

use App\Rules\TwoFactorToken;
use Illuminate\Foundation\Http\FormRequest;

class VerifiedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->user()->isTwoFactorEnabled()) {
            return [
                'token' => [
                    'required', 'string', 'max:100',
                    new TwoFactorToken,
                ],
            ];
        } else {
            return [
                'password' => [
                    'required', 'string', 'max:100',
                    'current_password',
                ],
            ];
        }
    }
}
