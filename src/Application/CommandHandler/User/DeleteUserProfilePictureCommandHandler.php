<?php

declare(strict_types=1);

namespace App\Application\CommandHandler\User;

use App\Application\Command\User\DeleteUserProfilePictureCommand;
use App\Domain\Model\User\User;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteUserProfilePictureCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(DeleteUserProfilePictureCommand $command): void
    {
        /**
         * @var User|null $user
         */
        $user = $this->userRepository->find($command->getUserId());

        if (null === $user) {
            throw new \RuntimeException('User not found');
        }

        $fileSystem = new Filesystem();
        $fileSystem->remove([$user->getProfileImagePath()]);
        $user->setProfileImagePath(null);

        $this->userRepository->save($user);
    }
}
