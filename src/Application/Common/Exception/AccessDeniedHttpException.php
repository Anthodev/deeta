<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

class AccessDeniedHttpException extends ForbiddenHttpException
{
    public function __construct(string $message = 'You do not have sufficient permissions to access this resource.')
    {
        parent::__construct($message);
    }
}
