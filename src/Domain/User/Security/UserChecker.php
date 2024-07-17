<?php

declare(strict_types=1);

namespace App\Domain\User\Security;

use App\Domain\User\Exception\UserDisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @throws UserDisabledException
     */
    #[\Override]
    public function checkPreAuth(UserInterface $user): void
    {
        /** @phpstan-ignore-next-line */
        if (false === $user->isEnabled()) {
            throw new UserDisabledException();
        }
    }

    #[\Override]
    public function checkPostAuth(UserInterface $user): void
    {
    }
}
