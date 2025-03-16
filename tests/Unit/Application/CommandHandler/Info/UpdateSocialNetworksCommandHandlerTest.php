<?php

declare(strict_types=1);

use App\Application\Command\Info\UpdateSocialNetworksCommand;
use App\Application\CommandHandler\Info\UpdateSocialNetworksCommandHandler;
use App\Domain\Model\Info\SocialNetwork;
use App\Domain\Model\User\User;
use App\Infrastructure\Persistence\Doctrine\Info\Repository\DoctrineSocialNetworkRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineUserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Ulid;

beforeEach(function () {
    $this->userRepository = $this->createMock(DoctrineUserRepository::class);
    $this->socialNetworkRepository = $this->createMock(DoctrineSocialNetworkRepository::class);
    $this->security = $this->createMock(Security::class);
    $this->handler = new UpdateSocialNetworksCommandHandler(
        $this->userRepository,
        $this->socialNetworkRepository,
        $this->security,
    );
});

it('can update a social network', function () {
    // Given
    /** @var User $user */
    $user = $this->makeMockedUser();
    /** @var SocialNetwork $socialNetwork */
    $socialNetwork = $this->createMock(SocialNetwork::class);

    $userId = Ulid::fromString($user->getId())->toBase32();
    $socialNetworkId = Ulid::generate();

    $command = new UpdateSocialNetworksCommand(
        $socialNetworkId,
        $userId,
        'GitHub',
        'GITHUB',
        'https://github.com/updated',
        2
    );

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with($userId)
        ->willReturn($user);

    $this->socialNetworkRepository
        ->expects($this->once())
        ->method('find')
        ->with($socialNetworkId)
        ->willReturn($socialNetwork);

    $this->security
        ->expects($this->once())
        ->method('getUser')
        ->willReturn($user);

    // The social network will be updated with these values
    $this->socialNetworkRepository
        ->expects($this->once())
        ->method('update')
        ->with($socialNetwork);

    // When
    $this->handler->__invoke($command);
});

it('cannot update a social network for a user that does not exist', function () {
    // Given
    $userId = Ulid::generate();
    $socialNetworkId = Ulid::generate();
    $command = new UpdateSocialNetworksCommand(
        $socialNetworkId,
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
        ->method('find');

    $this->socialNetworkRepository
        ->expects($this->never())
        ->method('update');

    // When & Then
    $this->handler->__invoke($command);
})->throws(RuntimeException::class, 'User not found');

it('cannot update a social network that does not exist', function () {
    // Given
    /** @var User $user */
    $user = $this->makeMockedUser();
    $userId = Ulid::fromString($user->getId())->toBase32();
    $socialNetworkId = Ulid::generate();

    $command = new UpdateSocialNetworksCommand(
        $socialNetworkId,
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
        ->method('find')
        ->with($socialNetworkId)
        ->willReturn(null);

    $this->socialNetworkRepository
        ->expects($this->never())
        ->method('update');

    // When & Then
    $this->handler->__invoke($command);
})->throws(RuntimeException::class, 'Social network not found');

it('cannot update a social network if user is not authorized', function () {
    // Given
    /** @var User $user */
    $user = $this->makeMockedUser();

    // Create a different user with a different ID
    $anotherUser = $this->createMock(User::class);
    $anotherUser->method('getId')->willReturn('different-id');

    /** @var SocialNetwork $socialNetwork */
    $socialNetwork = $this->createMock(SocialNetwork::class);

    $userId = Ulid::fromString($user->getId())->toBase32();
    $socialNetworkId = Ulid::generate();

    $command = new UpdateSocialNetworksCommand(
        $socialNetworkId,
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
        ->method('find')
        ->with($socialNetworkId)
        ->willReturn($socialNetwork);

    $this->security
        ->expects($this->once())
        ->method('getUser')
        ->willReturn($anotherUser);

    $this->socialNetworkRepository
        ->expects($this->never())
        ->method('update');

    // When & Then
    $this->handler->__invoke($command);
})->throws(RuntimeException::class, 'You are not allowed to update this social network');
