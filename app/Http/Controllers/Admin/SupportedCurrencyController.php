<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportedCurrencyResource;
use App\Models\SupportedCurrency;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use NeoScrypts\Exchanger\Drivers\AbstractDriver;
use Throwable;

class SupportedCurrencyController extends Controller
{
    /**
     * @var AbstractDriver
     */
    protected $exchangeDriver;

    protected string $baseCurrency;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:payments'));
        $this->baseCurrency = strtoupper(app('exchanger')->config('base_currency'));
        $this->exchangeDriver = app('exchanger')->getDriver();
    }

    /**
     * Paginate supported currency records
     */
    public function paginate(): AnonymousResourceCollection
    {
        $query = SupportedCurrency::with('statistic')->withCount('paymentAccounts');

        return SupportedCurrencyResource::collection(paginate($query));
    }

    /**
     * Get currencies
     */
    public function available(): Collection
    {
        return $this->availableCurrencies()->values();
    }

    /**
     * Add supported currency
     *
     *
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $available = $this->availableCurrencies();

        $validated = $this->validate($request, [
            'code' => ['required', Rule::in($available->pluck('code'))],
            'exchange_rate' => ['nullable', 'required_if:manual,true', 'numeric', 'min:0', 'max:999999'],
            'manual' => ['required', 'boolean'],
        ]);

        $currency = collect($available->firstWhere('code', $validated['code']));

        SupportedCurrency::create([
            'code' => $currency->get('code'),
            'name' => $currency->get('name'),
        ]);

        if ($validated['manual'] && $this->baseCurrency != $currency->get('code')) {
            $this->exchangeDriver->update($currency->get('code'), [
                'type' => 'manual',
                'exchange_rate' => $validated['exchange_rate'],
            ]);
        } else {
            $this->exchangeDriver->update($currency->get('code'), ['type' => 'auto']);

            Artisan::call('exchanger:update');
        }
    }

    /**
     * Delete supported currency
     *
     * @return never|void
     */
    public function delete(SupportedCurrency $currency)
    {
        if ($currency->default) {
            return abort(403, trans('auth.action_forbidden'));
        } else {
            $currency->delete();
        }
    }

    /**
     * Make default
     *
     * @return never|void
     *
     * @throws Throwable
     */
    public function makeDefault(SupportedCurrency $currency)
    {
        if ($currency->default) {
            return abort(403, trans('auth.action_forbidden'));
        }

        DB::transaction(function () use ($currency) {
            SupportedCurrency::default()->update(['default' => false]);

            $currency->update(['default' => true]);
        });
    }

    /**
     * Update limit
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function updateLimit(Request $request, SupportedCurrency $currency)
    {
        $validated = $this->validate($request, [
            'max_amount' => 'required|integer|gt:min_amount|max_digits:14',
            'min_amount' => 'required|integer|min:1|max_digits:14',
        ]);

        $currency->max_amount = $currency->parseMoney($validated['max_amount']);
        $currency->min_amount = $currency->parseMoney($validated['min_amount']);

        $currency->save();
    }

    /**
     * Update rate
     *
     *
     * @throws ValidationException
     */
    public function updateRate(Request $request, SupportedCurrency $currency)
    {
        $validated = $this->validate($request, [
            'exchange_rate' => ['nullable', 'required_if:manual,true', 'numeric', 'min:0', 'max:999999'],
            'manual' => ['required', 'boolean'],
        ]);

        if ($validated['manual'] && $this->baseCurrency != $currency->code) {
            $this->exchangeDriver->update($currency->code, [
                'type' => 'manual',
                'exchange_rate' => $validated['exchange_rate'],
            ]);
        } else {
            $this->exchangeDriver->update($currency->code, ['type' => 'auto']);

            Artisan::call('exchanger:update');
        }
    }

    /**
     * Available currencies
     */
    protected function availableCurrencies(): Collection
    {
        $existing = SupportedCurrency::all()->pluck('code')->toArray();
        $currencies = $this->exchangeDriver->all();

        return collect($currencies)->filter(function ($record) use ($existing) {
            return !in_array(Arr::get($record, 'code'), $existing)
                && is_numeric(Arr::get($record, 'exchange_rate'));
        });
    }
}
