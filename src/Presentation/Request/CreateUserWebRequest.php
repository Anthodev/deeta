<?php

declare(strict_types=1);

namespace App\Presentation\Request;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateUserWebRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Email]
        public ?string $email,
        #[Assert\NotBlank]
        public ?string $username,
        #[Assert\NotBlank, Assert\PasswordStrength, Assert\Length(min: 12, max: 32)]
        public ?string $password,
        #[Assert\NotBlank, Assert\EqualTo(propertyPath: 'password', message: 'The password fields must match.')]
        public ?string $passwordConfirm,
    ) {
    }
}
