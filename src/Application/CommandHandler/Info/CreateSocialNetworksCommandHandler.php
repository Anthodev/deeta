<?php

declare(strict_types=1);

namespace App\Application\CommandHandler\Info;

use App\Application\Command\Info\CreateSocialNetworksCommand;
use App\Domain\Model\Info\SocialNetwork;
use App\Domain\Repository\Info\SocialNetworkRepositoryInterface;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Ulid;

#[AsMessageHandler]
class CreateSocialNetworksCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SocialNetworkRepositoryInterface $socialNetworkRepository,
    ) {
    }

    public function __invoke(CreateSocialNetworksCommand $command): void
    {
        $userId = Ulid::fromString($command->getUserId());
        $user = $this->userRepository->find($userId->toBase32());

        if (null === $user) {
            throw new \RuntimeException('User not found');
        }

        $socialNetwork = new SocialNetwork(
            label: $command->getLabel(),
            url: $command->getUrl(),
            network: $command->getNetwork(),
            user: $user,
            position: $command->getPosition(),
        );

        $this->socialNetworkRepository->save($socialNetwork);
    }
}
