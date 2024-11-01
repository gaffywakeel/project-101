<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    /**
     * Get banks for user's country
     */
    public function all(): AnonymousResourceCollection
    {
        return BankResource::collection(Auth::user()->operatingBanks()->get());
    }
}
