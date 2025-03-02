<?php

declare(strict_types=1);

namespace App\Application\Command\User;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'sync')]
readonly class UpdateUserCommand
{
    public function __construct(
        private string $id,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $jobTitle = null,
        private ?string $company = null,
        private ?string $location = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }
}
