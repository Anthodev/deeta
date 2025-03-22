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
        id: '01H1234ABCD',
        label: 'PHP',
        position: 1,
        userId: '01HABCDEFGH',
        defaultColor: '#777BB3',
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
        'defaultColor' => '#777BB3',
    ]);
});

it('denormalizes JSON to a SkillDto object correctly', function () {
    // Given
    $json = json_encode([
        'id' => '01H1234ABCD',
        'label' => 'PHP',
        'position' => 1,
        'userId' => '01HABCDEFGH',
        'defaultColor' => '#777BB3',
    ]);

    // When
    $dto = $this->serializer->deserialize($json, SkillDto::class, 'json');

    // Then
    expect($dto)
        ->toBeInstanceOf(SkillDto::class)
        ->and($dto->id)->toBe('01H1234ABCD')
        ->and($dto->label)->toBe('PHP')
        ->and($dto->defaultColor)->toBe('#777BB3')
        ->and($dto->position)->toBe(1)
        ->and($dto->userId)->toBe('01HABCDEFGH');
});
