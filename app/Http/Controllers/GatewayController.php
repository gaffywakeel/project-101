<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaymentTransaction;
use App\Models\PaymentTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use NeoScrypts\Multipay\Drivers\AbstractDriver;

class GatewayController extends Controller
{
    /**
     * Handle gateway callback
     */
    public function handleReturn(Request $request, $order): RedirectResponse
    {
        $transaction = PaymentTransaction::findOrFail($order);
        $gateway = app('multipay')->gateway($transaction->gateway_name);
        $response = redirect()->route('index');

        switch ($gateway->handleReturn($request->input())) {
            case AbstractDriver::SUCCESS:
                ProcessPaymentTransaction::dispatch($transaction);

                return $response->notify(trans('payment.approved'), 'success');
            case AbstractDriver::FAILURE:
                return $response->notify(trans('payment.canceled'), 'error');
            case AbstractDriver::REDIRECT:
                return $response;
        }

        return $response->notify(trans('payment.unknown'), 'error');
    }

    /**
     * Handle notification
     */
    public function handleNotify(Request $request, $order): JsonResponse
    {
        $transaction = PaymentTransaction::findOrFail($order);
        $gateway = app('multipay')->gateway($transaction->gateway_name);

        if ($gateway->handleNotify($request->input()) == AbstractDriver::SUCCESS) {
            ProcessPaymentTransaction::dispatch($transaction);
        }

        return response()->json([], 202);
    }
}
