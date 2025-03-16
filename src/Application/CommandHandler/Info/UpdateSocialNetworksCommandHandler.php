<?php

declare(strict_types=1);

namespace App\Application\CommandHandler\Info;

use App\Application\Command\Info\UpdateSocialNetworksCommand;
use App\Domain\Model\User\User;
use App\Domain\Repository\Info\SocialNetworkRepositoryInterface;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Ulid;

#[AsMessageHandler]
class UpdateSocialNetworksCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SocialNetworkRepositoryInterface $socialNetworkRepository,
        private readonly Security $security,
    ) {
    }

    public function __invoke(
        UpdateSocialNetworksCommand $command,
    ): void {
        $socialNetworkId = Ulid::fromString($command->getSocialNetworkId());
        $userId = Ulid::fromString($command->getUserId());

        $user = $this->userRepository->find($userId->toBase32());

        if (null === $user) {
            throw new \RuntimeException('User not found');
        }

        $socialNetwork = $this->socialNetworkRepository->find($socialNetworkId->toBase32());

        if (null === $socialNetwork) {
            throw new \RuntimeException('Social network not found');
        }

        $securityUser = $this->security->getUser();

        if (!$securityUser instanceof User || $securityUser->getId() !== $user->getId()) {
            throw new \RuntimeException('You are not allowed to update this social network');
        }

        $socialNetwork->setLabel($command->getLabel());
        $socialNetwork->setUrl($command->getUrl());
        $socialNetwork->setNetwork($command->getNetwork());
        $socialNetwork->setPosition($command->getPosition());

        $this->socialNetworkRepository->update($socialNetwork);
    }
}
