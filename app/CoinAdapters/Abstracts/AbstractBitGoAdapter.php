<?php

namespace App\CoinAdapters\Abstracts;

use App\CoinAdapters\Contracts\Market;
use App\CoinAdapters\Exceptions\AdapterException;
use App\CoinAdapters\Exceptions\ValidationException;
use App\CoinAdapters\Resources\Address;
use App\CoinAdapters\Resources\Transaction;
use App\CoinAdapters\Resources\Wallet;
use App\CoinAdapters\Support\CoinCapCoinPrice;
use Brick\Math\BigDecimal;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

abstract class AbstractBitGoAdapter extends AbstractAdapter implements Market
{
    use CoinCapCoinPrice;

    /**
     * BitGo client request
     */
    protected PendingRequest $client;

    /**
     * BitGo's production identifier
     */
    protected string $bitgoIdentifier;

    /**
     * BitGo's test identifier
     */
    protected string $bitgoTestIdentifier;

    /**
     * Initialize bitgo request
     *
     * @return void
     */
    public function __construct()
    {
        $host = config('services.bitgo.host');
        $port = config('services.bitgo.port');

        $this->client = Http::baseUrl("$host:$port/api/v2/")->acceptJson()
            ->throw(fn ($response, $e) => $this->handleError($response, $e))
            ->withToken(config('services.bitgo.token'));
    }

    /**
     * Get adapter name
     */
    public function getAdapterName(): string
    {
        return "{$this->getName()} [BitGo]";
    }

    /**
     * Get BitGo identifier
     */
    protected function getBitgoIdentifier(): string
    {
        if (config('services.bitgo.env') === 'prod') {
            return $this->bitgoIdentifier;
        } else {
            return $this->bitgoTestIdentifier;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws ValidationException
     */
    public function createWallet(string $passphrase): Wallet
    {
        $response = $this->client->post("{$this->getBitgoIdentifier()}/wallet/generate", [
            'enterprise' => config('services.bitgo.enterprise'),
            'passphrase' => $passphrase,
            'label' => config('app.name'),
        ]);

        return $this->makeWalletResource($response->collect());
    }

    /**
     * {@inheritDoc}
     *
     * @throws ValidationException
     */
    public function createAddress(Wallet $wallet, string $passphrase, string $label = null): Address
    {
        $response = $this->client->post("{$this->getBitgoIdentifier()}/wallet/{$wallet->getId()}/address", ['label' => $label]);

        return $this->makeAddressResource($response->collect()->put('label', $label));
    }

    /**
     * {@inheritDoc}
     *
     * @throws AdapterException
     * @throws ValidationException
     */
    public function send(Wallet $wallet, string $address, string $amount, string $passphrase): Transaction
    {
        $response = $this->client->post("{$this->getBitgoIdentifier()}/wallet/{$wallet->getId()}/sendcoins", [
            'address' => $address,
            'walletPassphrase' => $passphrase,
            'amount' => $amount,
        ]);

        return $this->makeTransactionResource($response->collect('transfer'));
    }

    /**
     * Get transaction
     *
     * {@inheritDoc}
     *
     * @throws AdapterException
     * @throws ValidationException
     */
    public function getTransaction(Wallet $wallet, string $id): Transaction
    {
        $response = $this->client->get("{$this->getBitgoIdentifier()}/wallet/{$wallet->getId()}/transfer/$id");

        return $this->makeTransactionResource($response->collect());
    }

    /**
     * {@inheritDoc}
     */
    public function setTransactionWebhook(Wallet $wallet, int $minConf = 3): void
    {
        $this->client->post("{$this->getBitgoIdentifier()}/wallet/{$wallet->getId()}/webhooks", [
            'url' => $this->getTransactionWebhookUrl(),
            'type' => 'transfer',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function resetTransactionWebhook(Wallet $wallet, int $minConf = 3): void
    {
        $this->client->delete("{$this->getBitgoIdentifier()}/wallet/{$wallet->getId()}/webhooks", [
            'url' => $this->getTransactionWebhookUrl(),
            'type' => 'transfer',
        ]);

        $this->setTransactionWebhook($wallet, $minConf);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ValidationException
     * @throws AdapterException
     */
    public function handleTransactionWebhook(Wallet $wallet, array $payload): ?Transaction
    {
        $hash = data_get($payload, 'hash');

        return $hash ? $this->getTransaction($wallet, $hash) : null;
    }

    /**
     * Get fee estimate per byte
     */
    protected function getFeeEstimate(): string
    {
        $response = $this->client->get("{$this->getBitgoIdentifier()}/tx/fee");

        return (string) BigDecimal::of($response->json('feePerKb'))->exactlyDividedBy(1000);
    }

    /**
     * Make wallet resource
     *
     *
     * @throws ValidationException
     */
    protected function makeWalletResource(Collection $data): Wallet
    {
        return new Wallet([
            'id' => $data->get('id'),
            'data' => $data->toArray(),
        ]);
    }

    /**
     * Make address resource
     *
     *
     * @throws ValidationException
     */
    protected function makeAddressResource(Collection $data): Address
    {
        return new Address([
            'id' => $data->get('id'),
            'address' => $data->get('address'),
            'label' => $data->get('label'),
        ]);
    }

    /**
     * Make transaction resource
     *
     *
     * @throws AdapterException
     * @throws ValidationException
     */
    protected function makeTransactionResource(Collection $data): Transaction
    {
        return new Transaction([
            'id' => $data->get('id'),
            'hash' => $data->get('txid'),
            'type' => $data->get('type'),
            'confirmations' => $data->get('confirmations') ?: 0,
            'value' => $data->get('value') ?? $data->get('valueString'),
            'input' => $this->parseAddress($data->get('inputs')),
            'output' => $this->parseAddress($data->get('outputs')),
            'date' => $data->get('date') ?: now()->toDateString(),
        ]);
    }

    /**
     * Parse address array
     */
    protected function parseAddress($address): array|string|null
    {
        if (!is_array($address)) {
            return $address;
        }

        return collect($address)->map(fn ($data) => collect($data))->map(function (Collection $item) {
            if (!$item->has('value') && $item->has('valueString')) {
                $item->put('value', $item->get('valueString'));
            }

            return $item->only(['address', 'value'])->toArray();
        })->toArray();
    }

    /**
     * Handle request error
     *
     *
     * @throws AdapterException
     */
    protected function handleError(Response $response, RequestException $e): void
    {
        if ($message = $response->json('error')) {
            $context = $this->getIdentifier();
            throw new AdapterException("[$context] $message", $response->status());
        }
    }
}
