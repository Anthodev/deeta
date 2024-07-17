<?php

declare(strict_types=1);

namespace App\Domain\User\EventSubscriber;

use App\Domain\User\Enum\UserEventEnum;
use App\Domain\User\Event\UserChangedPasswordEvent;
use App\Domain\User\Fetcher\UserFetcherInterface;
use App\Domain\User\Security\PasswordChanger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class UserChangedPasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PasswordChanger $passwordChanger,
        private UserFetcherInterface $userFetcher,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEventEnum::USER_CHANGED_PASSWORD->value => 'userChangedPassword',
        ];
    }

    public function userChangedPassword(UserChangedPasswordEvent $event): void
    {
        $user = $event->getUser();
        $plainPassword = $event->getPlainPassword();

        $this->passwordChanger->changePassword($user, $plainPassword);

        $this->userFetcher->update($user);
    }
}
