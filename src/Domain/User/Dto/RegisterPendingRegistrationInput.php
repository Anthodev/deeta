<?php

declare(strict_types=1);

namespace App\Domain\User\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RegisterPendingRegistrationInput
{
    public function __construct(
        #[Assert\NotBlank, Assert\Email]
        private string $email,

        #[Assert\NotBlank, Assert\Length(min: 3, max: 255)]
        private string $username,

        #[Assert\NotBlank, Assert\Length(min: 12, max: 255)]
        #[\SensitiveParameter] private string $password,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
