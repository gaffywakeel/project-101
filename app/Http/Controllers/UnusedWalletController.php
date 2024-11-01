<?php

namespace App\Http\Controllers;

use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class UnusedWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $used = Auth::user()->walletAccounts()->pluck('wallet_id');

        $wallets = Wallet::whereNotIn('id', $used)->get();

        return WalletResource::collection($wallets);
    }
}
