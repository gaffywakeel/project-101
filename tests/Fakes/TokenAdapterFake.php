<?php

namespace Tests\Fakes;

use App\CoinAdapters\Contracts\Consolidation;
use App\CoinAdapters\Contracts\NativeAsset;
use App\CoinAdapters\Resources\Wallet;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TokenAdapterFake extends CoinAdapterFake implements Consolidation, NativeAsset
{
    /**
     * Fee address resources
     */
    protected Collection $feeAddresses;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        string $name,
        string $identifier,
        string $symbol,
        string $baseUnit,
        protected string $nativeAssetId,
        int $precision,
        int $currencyPrecision = 2,
    ) {
        parent::__construct($name, $identifier, $symbol, $baseUnit, $precision, $currencyPrecision, true);
        $this->feeAddresses = collect();
    }

    /**
     * {@inheritDoc}
     */
    public function consolidate(Wallet $wallet, string $address, string $passphrase): void
    {
        // nothing
    }

    /**
     * {@inheritDoc}
     */
    public function createWallet(string $passphrase): Wallet
    {
        return tap(parent::createWallet($passphrase), function (Wallet $wallet) {
            $this->feeAddresses->put($wallet->getId(), (string) Str::ulid());
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getFeeAddress(Wallet $wallet): string
    {
        return $this->feeAddresses->get($wallet->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function getNativeAssetId(): string
    {
        return $this->nativeAssetId;
    }
}
