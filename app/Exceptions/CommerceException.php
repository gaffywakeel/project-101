<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CommerceException extends HttpException
{
    /**
     * Construct Commerce Exception
     */
    public function __construct(string $message = '', int $statusCode = 422)
    {
        parent::__construct($statusCode, $message);
    }
}
