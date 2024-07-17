<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InternalServerHttpException extends HttpException
{
    /**
     * @param array<string, mixed> $headers
     */
    public function __construct(?string $message = null, ?\Throwable $previous = null, array $headers = [], int $code = 0)
    {
        parent::__construct(Response::HTTP_INTERNAL_SERVER_ERROR, $message ?? 'That should not happen. Please contact the administrator.', $previous, $headers, $code);
    }
}
