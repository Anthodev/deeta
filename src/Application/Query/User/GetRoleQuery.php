<?php

declare(strict_types=1);

namespace App\Application\Query\User;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'sync')]
class GetRoleQuery
{
    public function __construct(
        public readonly string $roleCode,
    ) {
    }
}
