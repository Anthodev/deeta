<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\Serializer;

use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineUserRepository;
use App\Shared\Dto\Info\SkillDto;
use Symfony\Component\Serializer\SerializerInterface;

beforeEach(function () {
    $this->serializer = static::$client->getContainer()->get(SerializerInterface::class);
    $this->userRepository = static::$client->getContainer()->get(DoctrineUserRepository::class);
});

it('normalizes a SkillDto object correctly', function () {
    // Given
    $dto = new SkillDto(
        skillId: '01H1234ABCD',
        skillLabel: 'PHP',
        skillPosition: 1,
        skillUserId: '01HABCDEFGH',
        skillIsTextWhite: false,
        skillDefaultColor: '#777BB3',
        __dto_type: 'skill',
    );

    // When
    $json = $this->serializer->serialize($dto, 'json');

    // Then
    $data = json_decode($json, true);
    expect($data)->toBe([
        'id' => '01H1234ABCD',
        'label' => 'PHP',
        'position' => 1,
        'userId' => '01HABCDEFGH',
        'isTextWhite' => false,
        'defaultColor' => '#777BB3',
        '__type' => 'skill',
    ]);
});

it('denormalizes JSON to a SkillDto object correctly', function () {
    // Given
    $json = json_encode([
        'id' => '01H1234ABCD',
        'label' => 'PHP',
        'position' => 1,
        'userId' => '01HABCDEFGH',
        'isTextWhite' => false,
        'defaultColor' => '#777BB3',
        '__type' => 'skill',
    ]);

    // When
    $dto = $this->serializer->deserialize($json, SkillDto::class, 'json');

    // Then
    expect($dto)
        ->toBeInstanceOf(SkillDto::class)
        ->and($dto->skillId)->toBe('01H1234ABCD')
        ->and($dto->skillLabel)->toBe('PHP')
        ->and($dto->skillDefaultColor)->toBe('#777BB3')
        ->and($dto->skillPosition)->toBe(1)
        ->and($dto->skillUserId)->toBe('01HABCDEFGH')
        ->and($dto->skillIsTextWhite)->toBeFalse();
});
