<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;

class CoinAdapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Wallet::class);

        $available = resolve('coin.manager')->all()->filter(function (array $data) {
            return Coin::whereIdentifier($data['identifier'])->doesntExist();
        });

        return response()->json($available->values());
    }
}
