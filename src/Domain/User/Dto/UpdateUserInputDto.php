<?php

declare(strict_types=1);

namespace App\Domain\User\Dto;

use App\Application\Common\Dto\InputDtoInterface;

readonly class UpdateUserInputDto implements InputDtoInterface
{
    public function __construct(
        private ?string $email = null,
        private ?string $username = null,
        private ?string $plainPassword = null,
    ) {
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
}
