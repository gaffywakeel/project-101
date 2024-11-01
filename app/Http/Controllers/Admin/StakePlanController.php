<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\StakePlanResource;
use App\Models\StakePlan;
use App\Models\Wallet;
use App\Rules\Decimal;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class StakePlanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:stakes'));
    }

    /**
     * Paginate StakePlans
     */
    public function paginate(): AnonymousResourceCollection
    {
        $records = StakePlan::latest()->withCount('stakes')->autoPaginate();

        return StakePlanResource::collection($records);
    }

    /**
     * Create StakePlan
     *
     * @return mixed
     *
     * @throws Throwable
     */
    public function create(Request $request)
    {
        $validated = $this->validateRequest($request, [
            'wallet' => ['required', 'integer', 'exists:wallets,id'],
        ]);

        return DB::transaction(function () use ($validated) {
            $plan = new StakePlan();

            $wallet = Wallet::find($validated['wallet']);
            $plan->fill(Arr::only($validated, ['title']));
            $plan->wallet()->associate($wallet)->save();

            $plan->rates()->createMany($validated['rates']);

            return StakePlanResource::make($plan);
        });
    }

    /**
     * Update StakePlan
     *
     * @return mixed
     *
     * @throws Throwable
     */
    public function update(Request $request, StakePlan $plan)
    {
        $validated = $this->validateRequest($request, [
            'rates.*.id' => ['required', 'integer', 'exists:stake_rates,id'],
        ]);

        return DB::transaction(function () use ($plan, $validated) {
            collect($validated['rates'])->each(function ($data) use ($plan) {
                $plan->rates()->find(Arr::pull($data, 'id'))->update($data);
            });

            $plan->update(Arr::only($validated, ['title']));

            return StakePlanResource::make($plan);
        });
    }

    /**
     * Delete StakePlan
     *
     * @return void
     */
    public function delete(StakePlan $plan)
    {
        $plan->delete();
    }

    /**
     * Validate StakePlan data
     */
    protected function validateRequest(Request $request, array $rules = []): array
    {
        $rules = array_merge([
            'title' => ['required', 'string', 'max:250'],
            'rates' => ['required', 'array', 'min:1', 'max:5'],
            'rates.*' => ['required', 'array:id,days,rate,min_value,max_value'],
            'rates.*.days' => ['required', 'integer', 'min:1', 'max:10000'],
            'rates.*.rate' => ['required', 'numeric', 'gt:0', 'max:100'],
            'rates.*.max_value' => ['required', 'numeric', 'gt:rates.*.min_value', new Decimal],
            'rates.*.min_value' => ['required', 'numeric', 'gt:0', new Decimal],
        ], $rules);

        return $request->validate($rules);
    }
}
