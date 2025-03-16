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

it('normalizes an array of SocialNetworkDto objects correctly', function () {
    // Given
    $dtos = [
        new SocialNetworkDto(
            id: '01H1234ABCD',
            network: 'GITHUB',
            label: 'GitHub',
            url: 'https://github.com/test',
            position: 1,
            userId: '01HABCDEFGH'
        ),
        new SocialNetworkDto(
            id: '01H5678EFGH',
            network: 'LINKEDIN',
            label: 'LinkedIn',
            url: 'https://linkedin.com/in/test',
            position: 2,
            userId: '01HABCDEFGH'
        ),
    ];

    // When
    $json = $this->serializer->serialize($dtos, 'json');

    // Then
    $data = json_decode($json, true);
    expect($data)->toBe([
        [
            'id' => '01H1234ABCD',
            'network' => 'GITHUB',
            'label' => 'GitHub',
            'url' => 'https://github.com/test',
            'position' => 1,
            'userId' => '01HABCDEFGH',
        ],
        [
            'id' => '01H5678EFGH',
            'network' => 'LINKEDIN',
            'label' => 'LinkedIn',
            'url' => 'https://linkedin.com/in/test',
            'position' => 2,
            'userId' => '01HABCDEFGH',
        ],
    ]);
});

it('denormalizes JSON to an array of SocialNetworkDto objects correctly', function () {
    // Given
    $json = json_encode([
        [
            'id' => '01H1234ABCD',
            'network' => 'GITHUB',
            'label' => 'GitHub',
            'url' => 'https://github.com/test',
            'position' => 1,
            'userId' => '01HABCDEFGH',
        ],
        [
            'id' => '01H5678EFGH',
            'network' => 'LINKEDIN',
            'label' => 'LinkedIn',
            'url' => 'https://linkedin.com/in/test',
            'position' => 2,
            'userId' => '01HABCDEFGH',
        ],
    ]);

    // When
    $dtos = $this->serializer->deserialize($json, 'array', 'json');

    // Then
    expect($dtos)
        ->toBeArray()
        ->and(count($dtos))->toBe(2)
        ->and($dtos[0])->toBeInstanceOf(SocialNetworkDto::class)
        ->and($dtos[0]->id)->toBe('01H1234ABCD')
        ->and($dtos[0]->network)->toBe('GITHUB')
        ->and($dtos[0]->label)->toBe('GitHub')
        ->and($dtos[0]->url)->toBe('https://github.com/test')
        ->and($dtos[0]->position)->toBe(1)
        ->and($dtos[0]->userId)->toBe('01HABCDEFGH')
        ->and($dtos[1])->toBeInstanceOf(SocialNetworkDto::class)
        ->and($dtos[1]->id)->toBe('01H5678EFGH')
        ->and($dtos[1]->network)->toBe('LINKEDIN')
        ->and($dtos[1]->label)->toBe('LinkedIn')
        ->and($dtos[1]->url)->toBe('https://linkedin.com/in/test')
        ->and($dtos[1]->position)->toBe(2)
        ->and($dtos[1]->userId)->toBe('01HABCDEFGH');
});

it('loads social networks from fixtures and normalizes them to an array of DTOs', function () {
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

    $json = $this->serializer->serialize($dtos, 'json');
    $data = json_decode($json, true);

    // Then
    expect(count($data))->toBe(3);

    foreach ($data as $item) {
        expect($item)->toHaveKeys(['id', 'network', 'label', 'url', 'position', 'userId']);
    }

    expect($data[0]['label'])
        ->toBe('GitHub')
        ->and($data[0]['network'])->toBe('GITHUB')
        ->and($data[0]['url'])->toBe('https://github.com/admin')
        ->and($data[1]['label'])->toBe('LinkedIn')
        ->and($data[1]['network'])->toBe('LINKEDIN')
        ->and($data[1]['url'])->toBe('https://linkedin.com/in/admin')
        ->and($data[2]['label'])->toBe('X')
        ->and($data[2]['network'])->toBe('X')
        ->and($data[2]['url'])->toBe('https://twitter.com/admin');
});
