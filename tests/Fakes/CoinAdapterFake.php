<?php

namespace Tests\Fakes;

use App\CoinAdapters\Contracts\Adapter;
use App\CoinAdapters\Exceptions\AdapterException;
use App\CoinAdapters\Exceptions\ValidationException;
use App\CoinAdapters\Resources\Address;
use App\CoinAdapters\Resources\Transaction;
use App\CoinAdapters\Resources\Wallet;
use Brick\Math\BigDecimal;
use Illuminate\Support\Collection;
use Str;

class CoinAdapterFake implements Adapter
{
    /**
     * Wallet resources
     */
    protected Collection $wallets;

    /**
     * Address resources
     */
    protected Collection $addresses;

    /**
     * Transaction resources
     */
    protected Collection $transactions;

    /**
     * Wallet passphrases
     */
    protected Collection $passphrases;

    /**
     * Dollar price
     */
    protected float $dollarPrice = 50;

    /**
     * Dollar price change
     */
    protected float $dollarPriceChange = 1.5;

    /**
     * Transaction fee rate
     */
    protected float $feeRate = 0.2;

    /**
     * Minimum unit
     */
    protected float $minimumUnit = 1;

    /**
     * Maximum unit
     */
    protected float $maximumUnit = 100000;

    /**
     * Construct CoinAdapterFake
     */
    public function __construct(
        protected string $name,
        protected string $identifier,
        protected string $symbol,
        protected string $baseUnit,
        protected int $precision,
        protected int $currencyPrecision = 2,
        protected bool $isAccountBased = false
    ) {
        $this->wallets = collect();
        $this->passphrases = collect();
        $this->addresses = collect();
        $this->transactions = collect();
    }

    /**
     * {@inheritDoc}
     */
    public function getAdapterName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getBaseUnit(): string
    {
        return $this->baseUnit;
    }

    /**
     * {@inheritDoc}
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyPrecision(): int
    {
        return $this->currencyPrecision;
    }

    /**
     * {@inheritDoc}
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * {@inheritDoc}
     */
    public function showSymbolFirst(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getColor(): string
    {
        return '#AD7B16';
    }

    /**
     * {@inheritDoc}
     */
    public function getSvgIcon(): string
    {
        return fake()->imageUrl(50, 50);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ValidationException
     */
    public function createWallet(string $passphrase): Wallet
    {
        $wallet = new Wallet(['id' => (string) Str::uuid()]);

        $this->wallets->put($wallet->getId(), $wallet);

        $this->passphrases->put($wallet->getId(), $passphrase);

        return $this->wallets->get($wallet->getId());
    }

    /**
     * {@inheritDoc}
     *
     * @throws AdapterException|ValidationException
     */
    public function createAddress(Wallet $wallet, string $passphrase, string $label = null): Address
    {
        $this->validateWallet($wallet);
        $this->validatePassphrase($wallet, $passphrase);

        $address = new Address([
            'id' => (string) Str::uuid(),
            'address' => (string) Str::ulid(),
            'label' => $label,
        ]);

        $this->addresses->put($key = "{$wallet->getId()}:{$address->getId()}", $address);

        return $this->addresses->get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @throws AdapterException|ValidationException
     */
    public function send(Wallet $wallet, string $address, string $amount, string $passphrase): Transaction
    {
        $this->validateWallet($wallet);
        $this->validatePassphrase($wallet, $passphrase);

        $fromAddress = (string) Str::ulid();

        $transaction = new Transaction([
            'id' => (string) Str::uuid(),
            'hash' => (string) Str::ulid(),
            'type' => 'send',
            $this->inputAttribute() => $this->formatAccount($fromAddress, $amount),
            $this->outputAttribute() => $this->formatAccount($address, $amount),
            'value' => $amount,
            'confirmations' => 6,
            'date' => now()->toJSON(),
        ]);

        $this->transactions->put($key = "{$wallet->getId()}:{$transaction->getId()}", $transaction);

        return $this->transactions->get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @throws AdapterException
     */
    public function getTransaction(Wallet $wallet, string $id): Transaction
    {
        $this->validateTransaction($wallet, $id);

        return $this->transactions->get("{$wallet->getId()}:$id");
    }

    /**
     * {@inheritDoc}
     *
     * @throws AdapterException|ValidationException
     */
    public function handleTransactionWebhook(Wallet $wallet, array $payload): ?Transaction
    {
        $this->validateWallet($wallet);

        $transaction = new Transaction([
            'id' => $payload['id'],
            'hash' => $payload['hash'],
            'type' => $payload['type'],
            $this->inputAttribute() => $this->formatAccount($payload['from'], $payload['value']),
            $this->outputAttribute() => $this->formatAccount($payload['to'], $payload['value']),
            'value' => $payload['value'],
            'confirmations' => $payload['confirmations'],
            'date' => $payload['date'],
        ]);

        $this->transactions->put($key = "{$wallet->getId()}:{$transaction->getId()}", $transaction);

        return $this->transactions->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function setTransactionWebhook(Wallet $wallet, int $minConf = 3): void
    {
        // nothing
    }

    /**
     * {@inheritDoc}
     */
    public function resetTransactionWebhook(Wallet $wallet, int $minConf = 3): void
    {
        // nothing
    }

    /**
     * {@inheritDoc}
     */
    public function getDollarPrice(): float
    {
        return $this->dollarPrice;
    }

    /**
     * Set fake dollar price
     */
    public function setDollarPrice(float $price): void
    {
        $this->dollarPrice = $price;
    }

    /**
     * {@inheritDoc}
     */
    public function getDollarPriceChange(): float
    {
        return $this->dollarPriceChange;
    }

    /**
     * Set fake dollar price
     */
    public function setDollarPriceChange(float $priceChange): void
    {
        $this->dollarPriceChange = $priceChange;
    }

    /**
     * {@inheritDoc}
     */
    public function getMarketChart(string $interval): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function estimateTransactionFee(string $amount, int $inputs): string
    {
        return (string) BigDecimal::of($amount)->multipliedBy($this->feeRate);
    }

    /**
     * Set fake fee rate
     */
    public function setFeeRate(float $rate): void
    {
        $this->feeRate = $rate;
    }

    /**
     * {@inheritDoc}
     */
    public function getMinimumTransferable(): string
    {
        return (string) BigDecimal::of($this->getBaseUnit())->multipliedBy($this->minimumUnit);
    }

    /**
     * Set minimum unit
     */
    public function setMinimumUnit(float $unit): void
    {
        $this->minimumUnit = $unit;
    }

    /**
     * {@inheritDoc}
     */
    public function getMaximumTransferable(): string
    {
        return (string) BigDecimal::of($this->getBaseUnit())->multipliedBy($this->maximumUnit);
    }

    /**
     * Set maximum unit
     */
    public function setMaximumUnit(float $unit): void
    {
        $this->maximumUnit = $unit;
    }

    /**
     * Transaction input attribute
     */
    protected function inputAttribute(): string
    {
        return $this->isAccountBased ? 'from' : 'input';
    }

    /**
     * Transaction output attribute
     */
    protected function outputAttribute(): string
    {
        return $this->isAccountBased ? 'to' : 'output';
    }

    /**
     * Format transaction address
     */
    protected function formatAccount(string $address, string|float $value): array|string
    {
        return $this->isAccountBased ? $address : [compact('address', 'value')];
    }

    /**
     * Validate existence of wallet resource
     *
     * @throws AdapterException
     */
    protected function validateWallet(Wallet $wallet): void
    {
        $context = $this->getIdentifier();

        if (!$this->wallets->has($wallet->getId())) {
            throw new AdapterException("[$context] Wallet not found.", 404);
        }
    }

    /**
     * Validate wallet passphrase
     *
     * @throws AdapterException
     */
    protected function validatePassphrase(Wallet $wallet, string $passphrase): void
    {
        $context = $this->getIdentifier();

        if ($this->passphrases->get($wallet->getId()) !== $passphrase) {
            throw new AdapterException("[$context] Wallet passphrase is invalid.", 403);
        }
    }

    /**
     * Validate transaction resource
     *
     * @throws AdapterException
     */
    protected function validateTransaction(Wallet $wallet, string $id): void
    {
        $context = $this->getIdentifier();

        if (!$this->transactions->has("{$wallet->getId()}:$id")) {
            throw new AdapterException("[$context] Transaction not found.", 404);
        }
    }
}
