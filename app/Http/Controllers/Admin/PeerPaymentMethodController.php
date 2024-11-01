<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeerPaymentMethodResource;
use App\Models\PeerPaymentMethod;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class PeerPaymentMethodController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:peer_trades'));
    }

    /**
     * Create Method
     *
     *
     * @throws ValidationException
     */
    public function create(Request $request): PeerPaymentMethodResource
    {
        $validated = $this->validateRequest($request);

        $method = PeerPaymentMethod::create($validated);

        return PeerPaymentMethodResource::make($method);
    }

    /**
     * Update method
     *
     *
     * @throws ValidationException
     */
    public function update(Request $request, PeerPaymentMethod $method): PeerPaymentMethodResource
    {
        $validated = $this->validateRequest($request);

        $method->update($validated);

        return PeerPaymentMethodResource::make($method);
    }

    /**
     * Delete Payment Method
     *
     * @return void
     */
    public function delete(PeerPaymentMethod $method)
    {
        $method->delete();
    }

    /**
     * Paginate payment methods
     */
    public function paginate(): AnonymousResourceCollection
    {
        $records = paginate(PeerPaymentMethod::latest());

        return PeerPaymentMethodResource::collection($records);
    }

    /**
     * Validate request
     *
     *
     * @throws ValidationException
     */
    protected function validateRequest(Request $request): array
    {
        return $this->validate($request, [
            'name' => ['required', 'string', 'max:250'],
            'category_id' => ['required', 'exists:peer_payment_categories,id'],
            'description' => ['required', 'string', 'max:1000'],
        ]);
    }
}
