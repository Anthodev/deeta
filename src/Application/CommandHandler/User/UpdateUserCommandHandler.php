<?php

declare(strict_types=1);

namespace App\Application\CommandHandler\User;

use App\Application\Command\User\UpdateUserCommand;
use App\Domain\Model\User\User;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

#[AsMessageHandler]
class UpdateUserCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(UpdateUserCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->find($command->getId());

        if (null === $user) {
            throw new \RuntimeException('User not found');
        }

        $user->setFirstName($command->getFirstName());
        $user->setLastName($command->getLastName());
        $user->setJobTitle($command->getJobTitle());
        $user->setCompany($command->getCompany());
        $user->setLocation($command->getLocation());

        $this->userRepository->save($user);
    }
}
