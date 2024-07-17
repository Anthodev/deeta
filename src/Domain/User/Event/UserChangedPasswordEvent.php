<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\User\Entity\User;
use App\Domain\User\Enum\UserEventEnum;
use Symfony\Contracts\EventDispatcher\Event;

class UserChangedPasswordEvent extends Event
{
    public const string NAME = UserEventEnum::USER_CHANGED_PASSWORD->value;

    public function __construct(
        private readonly User $user,
        #[\SensitiveParameter] private string $plainPassword,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }
}
