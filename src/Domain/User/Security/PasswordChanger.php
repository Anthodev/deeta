<?php

declare(strict_types=1);

namespace App\Domain\User\Security;

use App\Domain\User\Entity\PendingRegistration;
use App\Domain\User\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class PasswordChanger
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function changePassword(
        User|PendingRegistration $user,
        #[\SensitiveParameter] ?string $plainPassword = null,
    ): void {
        /** @var string $plainPassword */
        $plainPassword = $plainPassword ?? $user->getPlainPassword();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }
}
