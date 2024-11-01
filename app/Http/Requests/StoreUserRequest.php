<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\Username;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:25', User::uniqueRule(), new Username],
            'email' => ['required', 'string', 'email:rfc,dns,spoof', 'max:255', User::uniqueRule()],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed', Password::defaults()],
            'phone' => ['nullable', Rule::phone()->international(), 'max:255', User::uniqueRule()],
            'recaptcha' => getRecaptchaRules(),
        ];
    }
}
