<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeerPaymentCategoryResource;
use App\Models\PeerPaymentCategory;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PeerPaymentCategoryController extends Controller
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
     * Get all categories
     */
    public function all(): AnonymousResourceCollection
    {
        return PeerPaymentCategoryResource::collection(PeerPaymentCategory::all());
    }

    /**
     * Paginate category
     */
    public function paginate(): AnonymousResourceCollection
    {
        $records = paginate(PeerPaymentCategory::latest()->withCount('methods'));

        return PeerPaymentCategoryResource::collection($records);
    }

    /**
     * Create Category
     *
     *
     * @throws ValidationException
     */
    public function create(Request $request): PeerPaymentCategoryResource
    {
        $validated = $this->validate($request, [
            'name' => ['required', 'string', 'max:250', 'unique:peer_payment_categories'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        $category = PeerPaymentCategory::create($validated);

        return PeerPaymentCategoryResource::make($category);
    }

    /**
     * Update Category
     *
     *
     * @throws ValidationException
     */
    public function update(Request $request, PeerPaymentCategory $category): PeerPaymentCategoryResource
    {
        $validated = $this->validate($request, [
            'name' => ['required', 'string', 'max:250', Rule::unique('peer_payment_categories')->ignore($category)],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        $category->update($validated);

        return PeerPaymentCategoryResource::make($category);
    }

    /**
     * Delete category
     *
     * @return void
     */
    public function delete(PeerPaymentCategory $category)
    {
        $category->delete();
    }
}
