<?php

namespace App\Http\Controllers;

use App\Http\Resources\PeerPaymentMethodResource;
use App\Models\PeerPaymentMethod;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PeerPaymentMethodController extends Controller
{
    /**
     * Get payment methods
     */
    public function all(): AnonymousResourceCollection
    {
        $methods = PeerPaymentMethod::query()->orderBy('category_id')->get();

        return PeerPaymentMethodResource::collection($methods);
    }
}
