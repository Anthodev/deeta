<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\Serializer;

use App\Domain\Model\User\User;
use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineUserRepository;
use App\Shared\Dto\Info\SocialNetworkDto;
use Symfony\Component\Serializer\SerializerInterface;

beforeEach(function () {
    $this->serializer = static::$client->getContainer()->get(SerializerInterface::class);
    $this->userRepository = static::$client->getContainer()->get(DoctrineUserRepository::class);
});

it('normalizes a SocialNetworkDto object correctly', function () {
    // Given
    $dto = new SocialNetworkDto(
        networkId: '01H1234ABCD',
        networkIcon: 'GITHUB',
        networkLabel: 'GitHub',
        networkUrl: 'https://github.com/test',
        networkPosition: 1,
        userId: '01HABCDEFGH'
    );

    // When
    $json = $this->serializer->serialize($dto, 'json');

    // Then
    $data = json_decode($json, true);
    expect($data)->toBe([
        'id' => '01H1234ABCD',
        'network' => 'GITHUB',
        'label' => 'GitHub',
        'url' => 'https://github.com/test',
        'position' => 1,
        'userId' => '01HABCDEFGH',
    ]);
});

it('denormalizes JSON to a SocialNetworkDto object correctly', function () {
    // Given
    $json = json_encode([
        'id' => '01H1234ABCD',
        'network' => 'GITHUB',
        'label' => 'GitHub',
        'url' => 'https://github.com/test',
        'position' => 1,
        'userId' => '01HABCDEFGH',
    ]);

    // When
    $dto = $this->serializer->deserialize($json, SocialNetworkDto::class, 'json');

    // Then
    expect($dto)
        ->toBeInstanceOf(SocialNetworkDto::class)
        ->and($dto->networkId)->toBe('01H1234ABCD')
        ->and($dto->networkIcon)->toBe('GITHUB')
        ->and($dto->networkLabel)->toBe('GitHub')
        ->and($dto->networkUrl)->toBe('https://github.com/test')
        ->and($dto->networkPosition)->toBe(1)
        ->and($dto->userId)->toBe('01HABCDEFGH')
        ->and($dto->__dto_type)->toBe('social_network');
});

it('loads social networks from fixtures and normalizes them to DTOs', function () {
    // Given
    /** @var User $adminUser */
    $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);

    // When
    $socialNetworks = $adminUser->getSocialNetworks();
    $dtos = [];

    foreach ($socialNetworks as $socialNetwork) {
        $dto = new SocialNetworkDto(
            networkId: $socialNetwork->getId(),
            networkIcon: $socialNetwork->getNetwork(),
            networkLabel: $socialNetwork->getLabel(),
            networkUrl: $socialNetwork->getUrl(),
            networkPosition: $socialNetwork->getPosition(),
            userId: $adminUser->getId()
        );

        $dtos[] = $dto;
    }

    // Then
    expect(count($dtos))
        ->toBe(3)
        ->and($dtos[0]->networkLabel)->toBe('GitHub')
        ->and($dtos[0]->networkIcon)->toBe('GITHUB')
        ->and($dtos[0]->networkUrl)->toBe('https://github.com/admin')
        ->and($dtos[0]->networkPosition)->toBe(1)
        ->and($dtos[1]->networkLabel)->toBe('LinkedIn')
        ->and($dtos[1]->networkIcon)->toBe('LINKEDIN')
        ->and($dtos[1]->networkUrl)->toBe('https://linkedin.com/in/admin')
        ->and($dtos[1]->networkPosition)->toBe(2)
        ->and($dtos[2]->networkLabel)->toBe('X')
        ->and($dtos[2]->networkIcon)->toBe('X')
        ->and($dtos[2]->networkUrl)->toBe('https://twitter.com/admin')
        ->and($dtos[2]->networkPosition)->toBe(3);
});
