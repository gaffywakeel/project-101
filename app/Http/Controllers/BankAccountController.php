<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifiedRequest;
use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /**
     * Get bank accounts
     */
    public function all(): AnonymousResourceCollection
    {
        $accounts = Auth::user()->activeBankAccounts()->get();

        return BankAccountResource::collection($accounts);
    }

    /**
     * Create bank account
     */
    public function create(VerifiedRequest $request)
    {
        Auth::user()->acquireLock(function (User $user) use ($request) {
            if ($user->activeBankAccounts()->count() >= 6) {
                abort(403, trans('bank.account_limit'));
            }

            if (!$user->country_operation) {
                abort(403, trans('bank.unavailable_country'));
            }

            $validated = $this->validate($request, [
                'bank_name' => 'required_if:bank_id,other|string|min:5|max:255',
                'note' => 'nullable|string|max:1000',
                'number' => 'required|string|max:255',
            ]);

            $account = new BankAccount();
            $account->fill($validated);

            if ($request->input('bank_id') == 'other') {
                $account->bank_name = $validated['bank_name'];
            } else {
                $bankId = $request->input('bank_id');
                $bank = $user->operatingBanks()->findOrFail((int) $bankId);
                $account->bank()->associate($bank);
            }

            $account->currency = $user->currency;
            $account->country = $user->country;

            $user->activeBankAccounts()->save($account);
        });
    }

    /**
     * Delete account
     */
    public function delete($id)
    {
        Auth::user()->activeBankAccounts()->findOrFail($id)->delete();
    }
}
