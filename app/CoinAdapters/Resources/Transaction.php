<?php

namespace App\CoinAdapters\Resources;

use App\CoinAdapters\Exceptions\AdapterException;
use App\CoinAdapters\Exceptions\ValidationException;
use Brick\Math\BigDecimal;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class Transaction extends Resource
{
    protected array $rules = [
        'id' => 'required|string',
        'hash' => 'required|string',
        'type' => 'required|in:send,receive',
        'value' => 'required|numeric',
        'confirmations' => 'required|numeric',
        'date' => 'required|string',
        'data' => 'nullable|array',
    ];

    /**
     * Transaction constructor.
     *
     *
     * @throws AdapterException
     * @throws ValidationException
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->validateAddress($this->getReceiver());
        $this->validateAddress($this->getSender());
    }

    /**
     * Get transaction id
     */
    public function getId(): string
    {
        return $this->get('id');
    }

    /**
     * Get transaction hash
     */
    public function getHash(): string
    {
        return $this->get('hash');
    }

    /**
     * Get date
     */
    public function getDate(): Carbon
    {
        return Carbon::parse($this->get('date'));
    }

    /**
     * Get confirmations
     */
    public function getConfirmations(): int
    {
        return $this->get('confirmations');
    }

    /**
     * Get transaction value
     */
    public function getValue(): string
    {
        return (string) BigDecimal::of($this->get('value'))->abs();
    }

    /**
     * Get sender
     */
    public function getSender(): array|string|null
    {
        return $this->get('input') ?: $this->get('from');
    }

    /**
     * Get receiver
     */
    public function getReceiver(): array|string|null
    {
        return $this->get('output') ?: $this->get('to');
    }

    /**
     * Get transaction type
     */
    public function getType(): string
    {
        return $this->get('type');
    }

    /**
     * Get data
     */
    public function getData(): ?array
    {
        return $this->get('data');
    }

    /**
     * Lock key for synchronization
     */
    public function lockKey(): string
    {
        return $this->getHash();
    }

    /**
     * Validate inputs and outputs
     *
     *
     * @throws AdapterException
     */
    protected function validateAddress($address)
    {
        if (!is_null($address) || $this->getType() !== 'send') {
            if (is_array($address)) {
                collect($address)->each(function ($item) {
                    if (!is_array($item) || !Arr::has($item, ['address', 'value'])) {
                        throw new AdapterException('Item must contain address, value pairs.');
                    }

                    if (!is_string($item['address']) || !is_numeric($item['value'])) {
                        throw new AdapterException('Item contains invalid address, value pairs');
                    }
                });
            } elseif (!is_string($address) || empty($address)) {
                throw new AdapterException('Invalid address provided.');
            }
        }
    }
}
