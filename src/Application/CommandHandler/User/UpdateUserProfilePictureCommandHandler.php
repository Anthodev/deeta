<?php

declare(strict_types=1);

namespace App\Application\CommandHandler\User;

use App\Application\Command\User\UpdateUserProfilePictureCommand;
use App\Domain\Model\User\User;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateUserProfilePictureCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(UpdateUserProfilePictureCommand $command): void
    {
        /**
         * @var User|null $user
         */
        $user = $this->userRepository->find($command->getUserId());

        if (null === $user) {
            throw new \RuntimeException('User not found');
        }

        if (
            null !== $user->getProfileImagePath()
            && file_exists($user->getProfileImagePath())
        ) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($user->getProfileImagePath());

            $user->setProfileImagePath(null);
        }

        if (null !== $command->getProfilePicturePath()) {
            $user->setProfileImagePath($command->getProfilePicturePath());
        }

        $this->userRepository->save($user);
    }
}
