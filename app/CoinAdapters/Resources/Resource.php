<?php

namespace App\CoinAdapters\Resources;

use App\CoinAdapters\Exceptions\ValidationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

abstract class Resource
{
    /**
     * Resource
     */
    protected Collection $resource;

    /**
     * Validation rules
     *
     * @var array|string[]
     */
    protected array $rules;

    /**
     * Resource constructor.
     *
     *
     * @throws ValidationException
     */
    public function __construct(array $data)
    {
        $this->parse($data);
    }

    /**
     * Get resource value
     */
    public function get(string $key): mixed
    {
        return $this->resource->get($key);
    }

    /**
     * @throws ValidationException
     */
    protected function parse(array $data): void
    {
        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            throw new ValidationException($validator, $data);
        }

        $this->resource = new Collection($data);
    }

    /**
     * Get json format
     */
    public function toJson(): string
    {
        return $this->resource->toJson();
    }

    /**
     * Serialize resource
     */
    public function __serialize(): array
    {
        return ["\0*\0resource" => $this->resource->toArray()];
    }

    /**
     * Unserialize resource from database
     */
    public function __unserialize(array $data): void
    {
        $this->resource = collect($data["\0*\0resource"]);
    }
}
