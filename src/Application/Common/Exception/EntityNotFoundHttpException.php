<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

class EntityNotFoundHttpException extends NotFoundHttpException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Data not found with id %s', $id));
    }
}
