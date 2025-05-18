<?php

declare(strict_types=1);

namespace App\Application\Command\User;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[AsMessage(transport: 'sync')]
readonly class CreateUserCommand
{
    public function __construct(
        #[Assert\NotBlank, Assert\Email]
        private string $email,
        #[Assert\NotBlank]
        private string $username,
        #[Assert\NotBlank, Assert\Length(min: 12)]
        private string $password,
        #[Assert\NotBlank, Assert\Length(min: 12), Assert\EqualTo(propertyPath: 'password')]
        private string $passwordConfirm,
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

    public function getPasswordConfirm(): string
    {
        return $this->passwordConfirm;
    }
}
