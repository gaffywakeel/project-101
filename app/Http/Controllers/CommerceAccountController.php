<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommerceAccountResource;
use App\Models\CommerceAccount;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CommerceAccountController extends Controller
{
    /**
     * Get commerce account
     */
    public function get(): CommerceAccountResource
    {
        return CommerceAccountResource::make(Auth::user()->commerceAccount);
    }

    /**
     * Create commerce account
     *
     *
     * @throws AuthorizationException
     */
    public function create(Request $request): CommerceAccountResource
    {
        $this->authorize('create', CommerceAccount::class);
        $validated = $this->validateInput($request);

        $commerceAccount = Auth::user()->commerceAccount()->create($validated);

        if ($logo = Arr::pull($validated, 'logo')) {
            $commerceAccount->logo = savePublicFile($logo, $commerceAccount->path());
        }

        $commerceAccount->save();

        return CommerceAccountResource::make($commerceAccount);
    }

    /**
     * Update commerce account
     */
    public function update(Request $request): CommerceAccountResource
    {
        $commerceAccount = Auth::user()->commerceAccount()->firstOrFail();
        $validated = $this->validateInput($request, $commerceAccount);

        if ($logo = Arr::pull($validated, 'logo')) {
            $commerceAccount->logo = savePublicFile($logo, $commerceAccount->path());
        }

        $commerceAccount->update($validated);

        return CommerceAccountResource::make($commerceAccount);
    }

    /**
     * Validate Input
     */
    protected function validateInput(Request $request, CommerceAccount $account = null): array
    {
        $uniqueRule = $account?->getUniqueRule() ?: CommerceAccount::uniqueRule();

        return $request->validate([
            'name' => ['required', 'string', 'min:10', 'max:250'],
            'logo' => ['nullable', 'mimetypes:image/png,image/jpeg', 'dimensions:ratio=1', 'file', 'max:100'],
            'website' => ['nullable', 'url', 'max:250', $uniqueRule],
            'phone' => ['nullable', Rule::phone()->international(), $uniqueRule],
            'email' => ['required', 'email', 'max:250', $uniqueRule],
            'about' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
