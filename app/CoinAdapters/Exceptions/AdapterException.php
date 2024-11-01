<?php

namespace App\CoinAdapters\Exceptions;

use Exception;

class AdapterException extends Exception
{
    protected array $context = [];

    /**
     * Set context
     */
    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get the exception's context information.
     */
    public function context(): array
    {
        return $this->context;
    }
}
