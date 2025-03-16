<?php

declare(strict_types=1);

use App\Application\Command\Info\CreateSocialNetworksCommand;
use App\Application\CommandHandler\Info\CreateSocialNetworksCommandHandler;
use App\Domain\Model\Info\SocialNetwork;
use App\Domain\Model\User\User;
use App\Infrastructure\Persistence\Doctrine\Info\Repository\DoctrineSocialNetworkRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineUserRepository;
use Symfony\Component\Uid\Ulid;

beforeEach(function () {
    $this->userRepository = $this->createMock(DoctrineUserRepository::class);
    $this->socialNetworkRepository = $this->createMock(DoctrineSocialNetworkRepository::class);
    $this->handler = new CreateSocialNetworksCommandHandler(
        $this->userRepository,
        $this->socialNetworkRepository,
    );
});

it('can create a social network', function () {
    // Given
    /** @var User $user */
    $user = $this->makeMockedUser();

    $userId = Ulid::fromString($user->getId())->toBase32();

    $command = new CreateSocialNetworksCommand(
        $userId,
        'GitHub',
        'GITHUB',
        'https://github.com/username',
        1
    );

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with($userId)
        ->willReturn($user);

    $this->socialNetworkRepository
        ->expects($this->once())
        ->method('save')
        ->with($this->callback(function (SocialNetwork $socialNetwork) use ($user, $command) {
            return $socialNetwork->getLabel() === $command->getLabel()
                && $socialNetwork->getUrl() === $command->getUrl()
                && $socialNetwork->getNetwork() === $command->getNetwork()
                && $socialNetwork->getUser() === $user
                && $socialNetwork->getPosition() === $command->getPosition();
        }));

    // When
    $this->handler->__invoke($command);
});

it('cannot create a social network for a user that does not exist', function () {
    // Given
    $userId = Ulid::generate();
    $command = new CreateSocialNetworksCommand(
        $userId,
        'GitHub',
        'GITHUB',
        'https://github.com/username',
        1
    );

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with($userId)
        ->willReturn(null);

    $this->socialNetworkRepository
        ->expects($this->never())
        ->method('save');

    // When & Then
    $this->handler->__invoke($command);
})->throws(RuntimeException::class, 'User not found');
