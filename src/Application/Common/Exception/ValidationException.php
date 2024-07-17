<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

class ValidationException extends \InvalidArgumentException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
