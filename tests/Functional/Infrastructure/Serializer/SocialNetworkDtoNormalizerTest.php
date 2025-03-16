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
        id: '01H1234ABCD',
        network: 'GITHUB',
        label: 'GitHub',
        url: 'https://github.com/test',
        position: 1,
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
        ->and($dto->id)->toBe('01H1234ABCD')
        ->and($dto->network)->toBe('GITHUB')
        ->and($dto->label)->toBe('GitHub')
        ->and($dto->url)->toBe('https://github.com/test')
        ->and($dto->position)->toBe(1)
        ->and($dto->userId)->toBe('01HABCDEFGH');
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
            id: $socialNetwork->getId(),
            network: $socialNetwork->getNetwork(),
            label: $socialNetwork->getLabel(),
            url: $socialNetwork->getUrl(),
            position: $socialNetwork->getPosition(),
            userId: $adminUser->getId()
        );

        $dtos[] = $dto;
    }

    // Then
    expect(count($dtos))
        ->toBe(3)
        ->and($dtos[0]->label)->toBe('GitHub')
        ->and($dtos[0]->network)->toBe('GITHUB')
        ->and($dtos[0]->url)->toBe('https://github.com/admin')
        ->and($dtos[0]->position)->toBe(1)
        ->and($dtos[1]->label)->toBe('LinkedIn')
        ->and($dtos[1]->network)->toBe('LINKEDIN')
        ->and($dtos[1]->url)->toBe('https://linkedin.com/in/admin')
        ->and($dtos[1]->position)->toBe(2)
        ->and($dtos[2]->label)->toBe('X')
        ->and($dtos[2]->network)->toBe('X')
        ->and($dtos[2]->url)->toBe('https://twitter.com/admin')
        ->and($dtos[2]->position)->toBe(3);
});
