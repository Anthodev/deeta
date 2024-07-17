<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Application\Common\Exception\UnauthorizedHttpException;

class UserDisabledException extends UnauthorizedHttpException
{
    public function __construct()
    {
        parent::__construct('User is not enabled.');
    }
}
