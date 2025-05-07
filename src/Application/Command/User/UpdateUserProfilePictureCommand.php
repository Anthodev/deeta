<?php

declare(strict_types=1);

namespace App\Application\Command\User;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'sync')]
readonly class UpdateUserProfilePictureCommand
{
    public function __construct(
        private string $userId,
        private ?string $newFilename = null,
        private ?string $profilePicturePath = null,
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getNewFilename(): ?string
    {
        return $this->newFilename;
    }

    public function getProfilePicturePath(): ?string
    {
        return $this->profilePicturePath;
    }
}
