<?php

namespace App\CoinAdapters\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;

class ValidationException extends Exception
{
    /**
     * The validator instance.
     */
    protected Validator $validator;

    /**
     * The status code to use for the response.
     */
    protected int $status = 500;

    /**
     * The data being validated
     */
    protected array $data;

    /**
     * Create a new exception instance.
     */
    public function __construct(Validator $validator, array $data = [])
    {
        parent::__construct('The adapter resource is invalid.');

        $this->validator = $validator;
        $this->data = $data;
    }

    /**
     * Get all the validation error messages.
     */
    public function errors(): array
    {
        return $this->validator->errors()->messages();
    }

    /**
     * Set the HTTP status code to be used for the response.
     */
    public function status(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the exception's context information.
     */
    public function context(): array
    {
        return [
            'errors' => $this->errors(),
            'data' => $this->data,
        ];
    }
}
