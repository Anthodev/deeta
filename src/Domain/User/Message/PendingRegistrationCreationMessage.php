<?php

declare(strict_types=1);

namespace App\Domain\User\Message;

readonly class PendingRegistrationCreationMessage
{
    public function __construct(
        private string $pendingRegistrationId,
    ) {
    }

    public function getPendingRegistrationId(): string
    {
        return $this->pendingRegistrationId;
    }
}
