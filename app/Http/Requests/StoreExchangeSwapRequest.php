<?php

namespace App\Http\Requests;

use App\Helpers\CoinFormatter;
use App\Models\ExchangeFee;
use App\Models\WalletAccount;
use App\Rules\Decimal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use UnexpectedValueException;

class StoreExchangeSwapRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'sell_value' => ['required_without:buy_value', 'numeric', 'gt:0', new Decimal],
            'buy_value' => ['required_without:sell_value', 'numeric', 'gt:0', new Decimal],
            'sell_account' => ['different:buy_account', 'required', 'numeric'],
            'buy_account' => ['different:sell_account', 'required', 'numeric'],
        ];
    }

    /**
     * Get swap conversion
     *
     * @return array{
     *     sell_value: CoinFormatter,
     *     buy_value: CoinFormatter,
     *     sell_account: WalletAccount,
     *     buy_account: WalletAccount
     * }
     */
    public function conversion(): array
    {
        $validated = $this->validated();

        $buyAccount = $this->getWalletAccount($validated['buy_account']);
        $sellAccount = $this->getWalletAccount($validated['sell_account']);

        $exchangeFee = $sellAccount->wallet->exchangeFees()
            ->whereCategory('sell')->first();

        if (Arr::has($validated, 'buy_value')) {
            $buyValue = $buyAccount->parseCoin($validated['buy_value']);

            $sellPrice = $buyValue->add($this->calculateFee($buyValue, $exchangeFee))->getPrice();
            $sellValue = $sellAccount->parseCoin($sellPrice / $sellAccount->wallet->getDollarPrice());
        } elseif (Arr::has($validated, 'sell_value')) {
            $sellValue = $sellAccount->parseCoin($validated['sell_value']);

            $buyPrice = $sellValue->subtract($this->calculateFee($sellValue, $exchangeFee))->getPrice();
            $buyValue = $buyAccount->parseCoin($buyPrice / $buyAccount->wallet->getDollarPrice());
        } else {
            throw new UnexpectedValueException();
        }

        return [
            'sell_value' => $sellValue,
            'buy_value' => $buyValue,
            'sell_account' => $sellAccount,
            'buy_account' => $buyAccount,
        ];
    }

    /**
     * Find WalletAccount
     */
    protected function getWalletAccount(int $id): WalletAccount
    {
        return $this->user()->walletAccounts()->findOrFail($id);
    }

    /**
     * Calculate swap fee
     */
    protected function calculateFee(CoinFormatter $value, ?ExchangeFee $exchangeFee): CoinFormatter
    {
        if ($exchangeFee) {
            return $value->multiply(min($exchangeFee->value, 99) / 100);
        } else {
            return $value->multiply(0);
        }
    }
}
