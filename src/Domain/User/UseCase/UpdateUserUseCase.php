<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Exception\BuildException;
use App\Domain\User\Builder\UserBuilder;
use App\Domain\User\Dto\UpdateUserInputDto;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\UserEventEnum;
use App\Domain\User\Event\UserChangedPasswordEvent;
use App\Domain\User\Fetcher\UserFetcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class UpdateUserUseCase
{
    public function __construct(
        private UserFetcherInterface $userFetcher,
        private UserBuilder $userBuilder,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws BuildException
     */
    public function updateUser(UpdateUserInputDto $userInputDto, User $user): void
    {
        /** @var User|EntityInterface $user */
        $user = $this->userBuilder->populate($userInputDto, $user);

        if (null !== $userInputDto->getPlainPassword()) {
            /** @var User $user */
            $this->eventDispatcher->dispatch(new UserChangedPasswordEvent($user, $userInputDto->getPlainPassword()), UserEventEnum::USER_CHANGED_PASSWORD->value);
        } else {
            $this->userFetcher->update($user);
        }
    }
}
