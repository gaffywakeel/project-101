<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Models\Coin;
use App\Models\Wallet;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommerceFeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:commerce'));
    }

    /**
     * Get commerce fees
     */
    public function all(): AnonymousResourceCollection
    {
        $wallets = Wallet::with('commerceFee')->get();

        return WalletResource::collection($wallets);
    }

    /**
     * Update commerce fee
     */
    public function update(Request $request)
    {
        $identifiers = Coin::all()->pluck('identifier');

        $validated = $this->validate($request, [
            'fees' => 'required|array:' . $identifiers->implode(','),
            'fees.*' => 'required|numeric|min:0|max:99',
        ]);

        foreach ($validated['fees'] as $identifier => $value) {
            $wallet = Wallet::identifier($identifier)->firstOrFail();
            $commerceFee = $wallet->commerceFee()->firstOrNew();

            $commerceFee->fill(compact('value'))->save();
        }
    }
}
