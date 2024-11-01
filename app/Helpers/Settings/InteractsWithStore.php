<?php

namespace App\Helpers\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Valuestore\Valuestore as ValueStore;
use UnexpectedValueException;

trait InteractsWithStore
{
    /**
     * Store instance
     */
    protected ValueStore $store;

    /**
     * Verification prefix
     */
    protected ?string $prefix;

    /**
     * Initialize store helper
     *
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(string $prefix = null)
    {
        $this->prefix = $prefix;
        $this->store = valueStore();
        $this->initialize();
    }

    /**
     * Initialize children
     */
    protected function initialize(): void
    {
        if (property_exists($this, 'children')) {
            collect($this->children)->each(function ($child, $key) {
                if (class_exists($child) && is_string($key)) {
                    $this->{$key} = new $child($this->key($key));
                }
            });
        }
    }

    /**
     * Get key name
     */
    protected function key($name): string
    {
        return Str::camel(is_string($this->prefix) ? "$this->prefix.$name" : $name);
    }

    /**
     * Put in store
     */
    public function put($name, $value): static
    {
        $this->validateName($name);

        $this->store->put($this->key($name), $value);

        return $this;
    }

    /**
     * Get from store
     */
    public function get($name): mixed
    {
        $default = $this->validateName($name);

        return $this->store->get($this->key($name), $default);
    }

    /**
     * Check if key exists in store
     */
    public function has($name): bool
    {
        return $this->store->has($this->key($name));
    }

    /**
     * Validate settings name
     */
    protected function validateName($name): mixed
    {
        $attributes = $this->attributes();

        if (!Arr::has($attributes, $name)) {
            throw new UnexpectedValueException("Unknown settings key: {$name}");
        }

        return Arr::get($attributes, $name);
    }

    /**
     * Get attributes
     */
    protected function attributes(): array
    {
        return !property_exists($this, 'attributes') ? [] : $this->attributes;
    }

    /**
     * Get all values
     */
    public function all(): array
    {
        return collect($this->attributes())->map(function ($default, $name) {
            return $this->store->get($this->key($name), $default);
        })->toArray();
    }
}
