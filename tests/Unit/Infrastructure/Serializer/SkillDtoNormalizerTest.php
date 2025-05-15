<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Serializer;

use App\Infrastructure\Serializer\SkillDtoNormalizer;
use App\Shared\Dto\Info\SkillDto;

it('normalizes a SkillDto correctly', function () {
    // Given
    $normalizer = new SkillDtoNormalizer();
    $skillDto = new SkillDto(
        skillId: '01H1234ABCD',
        skillLabel: 'PHP',
        skillPosition: 1,
        skillUserId: '01HABCDEFGH',
        skillIsTextWhite: true,
        skillDefaultColor: '#777BB3',
        __dto_type: 'skill',
    );

    // When
    $result = $normalizer->normalize($skillDto);

    // Then
    expect($result)->toBe([
        'id' => '01H1234ABCD',
        'label' => 'PHP',
        'position' => 1,
        'userId' => '01HABCDEFGH',
        'isTextWhite' => true,
        'defaultColor' => '#777BB3',
        '__type' => 'skill',
    ]);
});

it('supports normalization for SkillDto objects', function () {
    // Given
    $normalizer = new SkillDtoNormalizer();
    $skillDto = new SkillDto(
        skillId: '01H1234ABCD',
        skillLabel: 'PHP',
        skillPosition: 1,
        skillUserId: '01HABCDEFGH',
        skillIsTextWhite: true,
        skillDefaultColor: '#777BB3',
    );

    // When/Then
    expect($normalizer->supportsNormalization($skillDto))->toBeTrue();
    expect($normalizer->supportsNormalization(new \stdClass()))->toBeFalse();
});

it('denormalizes an array to a SkillDto correctly', function () {
    // Given
    $normalizer = new SkillDtoNormalizer();
    $data = [
        'id' => '01H1234ABCD',
        'label' => 'PHP',
        'position' => 1,
        'userId' => '01HABCDEFGH',
        'isTextWhite' => true,
        'defaultColor' => '#777BB3',
    ];

    // When
    $result = $normalizer->denormalize($data, SkillDto::class);

    // Then
    expect($result)
        ->toBeInstanceOf(SkillDto::class)
        ->and($result->skillId)->toBe('01H1234ABCD')
        ->and($result->skillLabel)->toBe('PHP')
        ->and($result->skillDefaultColor)->toBe('#777BB3')
        ->and($result->skillPosition)->toBe(1)
        ->and($result->skillUserId)->toBe('01HABCDEFGH')
        ->and($result->skillIsTextWhite)->toBeTrue();
});

it('supports denormalization for SkillDto class', function () {
    // Given
    $normalizer = new SkillDtoNormalizer();

    // When/Then
    expect($normalizer->supportsDenormalization([], SkillDto::class))->toBeTrue();
    expect($normalizer->supportsDenormalization([], 'App\Shared\Dto\Info\OtherDto'))->toBeFalse();
    expect($normalizer->supportsDenormalization('not an array', SkillDto::class))->toBeTrue();
});

it('declares the supported types', function () {
    // Given
    $normalizer = new SkillDtoNormalizer();

    // When
    $supportedTypes = $normalizer->getSupportedTypes(null);

    // Then
    expect($supportedTypes)
        ->toBeArray()
        ->toHaveKey(SkillDto::class)
        ->and($supportedTypes[SkillDto::class])->toBeTrue();
});
