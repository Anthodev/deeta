<?php

declare(strict_types=1);

use App\Infrastructure\Serializer\SocialNetworkDtoNormalizer;
use App\Shared\Dto\Info\SocialNetworkDto;

beforeEach(function () {
    $this->socialNetworkDtoNormalizer = new SocialNetworkDtoNormalizer();
});

it('normalizes a SocialNetworkDto object', function () {
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
    $result = $this->socialNetworkDtoNormalizer->normalize($dto);

    // Then
    expect($result)->toBe([
        'id' => '01H1234ABCD',
        'network' => 'GITHUB',
        'label' => 'GitHub',
        'url' => 'https://github.com/test',
        'position' => 1,
        'userId' => '01HABCDEFGH',
    ]);
});

it('throws an exception when normalizing non-SocialNetworkDto object', function () {
    // Given
    $invalidData = 'not a SocialNetworkDto';

    // When & Then
    $this->socialNetworkDtoNormalizer->normalize($invalidData);
})->throws(InvalidArgumentException::class, 'The object must be an instance of SocialNetworkDto');

it('supports normalization of SocialNetworkDto objects', function () {
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
    $result = $this->socialNetworkDtoNormalizer->supportsNormalization($dto);

    // Then
    expect($result)->toBeTrue();
});

it('does not support normalization of non-SocialNetworkDto objects', function () {
    // Given
    $nonDto = 'string';

    // When
    $result = $this->socialNetworkDtoNormalizer->supportsNormalization($nonDto);

    // Then
    expect($result)->toBeFalse();
});

it('denormalizes an array to a SocialNetworkDto object', function () {
    // Given
    $data = [
        'id' => '01H1234ABCD',
        'network' => 'GITHUB',
        'label' => 'GitHub',
        'url' => 'https://github.com/test',
        'position' => 1,
        'userId' => '01HABCDEFGH',
    ];

    // When
    $result = $this->socialNetworkDtoNormalizer->denormalize($data, SocialNetworkDto::class);

    // Then
    expect($result)->toBeInstanceOf(SocialNetworkDto::class);
    expect($result->id)->toBe('01H1234ABCD');
    expect($result->network)->toBe('GITHUB');
    expect($result->label)->toBe('GitHub');
    expect($result->url)->toBe('https://github.com/test');
    expect($result->position)->toBe(1);
    expect($result->userId)->toBe('01HABCDEFGH');
});

it('denormalizes with missing fields setting default values', function () {
    // Given
    $data = [
        'label' => 'GitHub',
    ];

    // When
    $result = $this->socialNetworkDtoNormalizer->denormalize($data, SocialNetworkDto::class);

    // Then
    expect($result)
        ->toBeInstanceOf(SocialNetworkDto::class)
        ->and($result->id)->toBe('')
        ->and($result->network)->toBe('')
        ->and($result->label)->toBe('GitHub')
        ->and($result->url)->toBe('')
        ->and($result->position)->toBe(0)
        ->and($result->userId)->toBe('');
});

it('denormalizes with type coercion for position', function () {
    // Given
    $data = [
        'id' => '01H1234ABCD',
        'network' => 'GITHUB',
        'label' => 'GitHub',
        'url' => 'https://github.com/test',
        'position' => '2',
        'userId' => '01HABCDEFGH',
    ];

    // When
    $result = $this->socialNetworkDtoNormalizer->denormalize($data, SocialNetworkDto::class);

    // Then
    expect($result->position)->toBe(2);
    expect($result->position)->toBeInt();
});

it('throws an exception when denormalizing non-array data', function () {
    // Given
    $invalidData = 'not an array';

    // When & Then
    $this->socialNetworkDtoNormalizer->denormalize($invalidData, SocialNetworkDto::class);
})->throws(InvalidArgumentException::class, 'Data is not an array');

it('supports denormalization to SocialNetworkDto class', function () {
    // Given
    $data = ['label' => 'GitHub'];

    // When
    $result = $this->socialNetworkDtoNormalizer->supportsDenormalization($data, SocialNetworkDto::class);

    // Then
    expect($result)->toBeTrue();
});

it('supports denormalization to SocialNetworkDto string type', function () {
    // Given
    $data = ['label' => 'GitHub'];

    // When
    $result = $this->socialNetworkDtoNormalizer->supportsDenormalization($data, 'App\Shared\Dto\Info\SocialNetworkDto');

    // Then
    expect($result)->toBeTrue();
});

it('does not support denormalization to other types', function () {
    // Given
    $data = ['label' => 'GitHub'];

    // When
    $result = $this->socialNetworkDtoNormalizer->supportsDenormalization($data, 'string');

    // Then
    expect($result)->toBeFalse();
});

it('returns correct supported types', function () {
    // When
    $result = $this->socialNetworkDtoNormalizer->getSupportedTypes(null);

    // Then
    expect($result)->toBe([SocialNetworkDto::class => true]);
});
