<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\User\Dto\RegisterPendingRegistrationInput;
use App\Domain\User\Entity\Role;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Factory\PendingRegistrationFactory;
use App\Domain\User\Fetcher\PendingRegistrationFetcherInterface;
use App\Domain\User\Fetcher\RoleFetcherInterface;
use App\Domain\User\Message\PendingRegistrationCreationMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;

readonly class RegisterPendingRegistrationUseCase
{
    public function __construct(
        private PendingRegistrationFetcherInterface $pendingRegistrationFetcher,
        private RoleFetcherInterface $roleFetcher,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function registerPendingRegistration(RegisterPendingRegistrationInput $registerUserInput): void
    {
        $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
            $registerUserInput->getEmail(),
            $registerUserInput->getUsername(),
            $registerUserInput->getPassword()
        );

        /** @var Role $role */
        $role = $this->roleFetcher->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]);

        $pendingRegistration->setRole($role);

        $token = md5($pendingRegistration->getEmail());
        $pendingRegistration->setToken($token);

        $this->pendingRegistrationFetcher->save($pendingRegistration);

        /** @var Ulid $pendingRegistrationId */
        $pendingRegistrationId = $pendingRegistration->getId();
        $this->messageBus->dispatch(new PendingRegistrationCreationMessage($pendingRegistrationId->toRfc4122()));
    }
}
