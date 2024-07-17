<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ForbiddenHttpException extends HttpException
{
    public function __construct(string $message = '')
    {
        parent::__construct(Response::HTTP_FORBIDDEN, $message);
    }
}
