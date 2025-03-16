<?php

declare(strict_types=1);

namespace App\Application\Query\Info;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'sync')]
readonly class GetSocialNetworksForUserQuery
{
    public function __construct(
        private string $userId,
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
