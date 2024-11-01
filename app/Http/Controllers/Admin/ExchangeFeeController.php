<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Models\Coin;
use App\Models\Wallet;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class ExchangeFeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:wallets'));
    }

    /**
     * Get exchange fee
     */
    public function all(): AnonymousResourceCollection
    {
        $records = Wallet::with('exchangeFees')->get();

        return WalletResource::collection($records);
    }

    /**
     * Update exchange fees
     *
     *
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $identifiers = Coin::all()->pluck('identifier');

        $validated = $this->validate($request, [
            'fees' => 'required|array:' . $identifiers->implode(','),
            'fees.*' => 'required|array:buy,sell',
            'fees.*.*' => 'required|numeric|min:0|max:99',
        ]);

        foreach ($validated['fees'] as $identifier => $categories) {
            $wallet = Wallet::identifier($identifier)->firstOrFail();

            foreach ($categories as $category => $value) {
                $exchangeFee = $wallet->exchangeFees()->firstOrNew(compact('category'));
                $exchangeFee->fill(compact('value'))->save();
            }
        }
    }
}
