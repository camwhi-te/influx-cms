<?php

namespace App\Exceptions;

class UnauthorizedException extends \RuntimeException
{
    public function __construct(string $message = 'Unauthorized', int $code = 403)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->code);
    }
}
