<?php

declare(strict_types=1);

namespace App\Tests\Unit\Presentation\Components\Admin;

use App\Domain\Model\Info\SocialNetwork;
use App\Domain\Model\User\User;
use App\Presentation\Components\Admin\MeSocialNetworks;
use App\Shared\Dto\Info\SocialNetworkDto;
use App\Shared\Enum\SocialNetworkEnum;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

beforeEach(function () {
    /** @var MockObject&MessageBusInterface */
    $this->messageBus = $this->createMock(MessageBusInterface::class);

    /** @var MockObject&LoggerInterface */
    $this->logger = $this->createMock(LoggerInterface::class);

    $this->component = new MeSocialNetworks(
        $this->messageBus,
        $this->logger
    );
});

it('initializes with social network options from enum', function () {
    // Then
    expect($this->component->socialNetworksOptions)
        ->toBe(SocialNetworkEnum::toLabelArray())
        ->and($this->component->errors)->toBe([]);
});

it('mounts with correct data transformation', function () {
    // Given
    $userId = '01H1234ABCD';

    /** @var MockObject&User */
    $user = $this->createMock(User::class);
    $user->method('getId')->willReturn($userId);

    $socialNetwork1 = new SocialNetwork(
        label: 'GitHub',
        url: 'https://github.com/user',
        network: 'GITHUB',
        user: $user,
        position: 1,
    );

    $socialNetwork1Id = '01HSOCNET1';
    $reflection = new \ReflectionClass($socialNetwork1);
    $idProperty = $reflection->getProperty('id');
    $idProperty->setAccessible(true);
    $idProperty->setValue($socialNetwork1, $socialNetwork1Id);

    $socialNetwork2 = new SocialNetwork(
        label: 'LinkedIn',
        url: 'https://linkedin.com/in/user',
        network: 'LINKEDIN',
        user: $user,
        position: 2,
    );

    $socialNetwork2Id = '01HSOCNET2';
    $reflection = new \ReflectionClass($socialNetwork2);
    $idProperty = $reflection->getProperty('id');
    $idProperty->setAccessible(true);
    $idProperty->setValue($socialNetwork2, $socialNetwork2Id);

    $socialNetworks = [$socialNetwork1, $socialNetwork2];

    // When
    $this->component->mount($userId, $socialNetworks);

    // Then
    expect($this->component->userId)
        ->toBe($userId)
        ->and($this->component->socialNetworks)->toBeArray()
        ->and(count($this->component->socialNetworks))->toBe(2)
        ->and($this->component->socialNetworks[0])->toBeInstanceOf(SocialNetworkDto::class)
        ->and($this->component->socialNetworks[0]->networkId)->toBe($socialNetwork1Id)
        ->and($this->component->socialNetworks[0]->networkIcon)->toBe('GITHUB')
        ->and($this->component->socialNetworks[0]->networkLabel)->toBe('GitHub')
        ->and($this->component->socialNetworks[0]->networkUrl)->toBe('https://github.com/user')
        ->and($this->component->socialNetworks[0]->networkPosition)->toBe(1)
        ->and($this->component->socialNetworks[0]->userId)->toBe($userId)
        ->and($this->component->socialNetworks[1])->toBeInstanceOf(SocialNetworkDto::class)
        ->and($this->component->socialNetworks[1]->networkId)->toBe($socialNetwork2Id)
        ->and($this->component->socialNetworks[1]->networkIcon)->toBe('LINKEDIN')
        ->and($this->component->socialNetworks[1]->networkLabel)->toBe('LinkedIn');
});
