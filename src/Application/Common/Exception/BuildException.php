<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

class BuildException extends \InvalidArgumentException
{
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
