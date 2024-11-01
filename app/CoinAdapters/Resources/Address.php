<?php

namespace App\CoinAdapters\Resources;

class Address extends Resource
{
    /**
     * Address validation rules
     *
     * @var array|string[]
     */
    protected array $rules = [
        'id' => 'required|string',
        'label' => 'nullable|string',
        'address' => 'required|string',
        'data' => 'nullable|array',
    ];

    /**
     * Get address' id
     */
    public function getId(): string
    {
        return $this->get('id');
    }

    /**
     * Get address' label
     */
    public function getLabel(): ?string
    {
        return $this->get('label');
    }

    /**
     * Get address
     */
    public function getAddress(): string
    {
        return $this->get('address');
    }

    /**
     * Get data
     */
    public function getData(): ?array
    {
        return $this->get('data');
    }
}
