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
        networkId: '01H1234ABCD',
        networkIcon: 'GITHUB',
        networkLabel: 'GitHub',
        networkUrl: 'https://github.com/test',
        networkPosition: 1,
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
        networkId: '01H1234ABCD',
        networkIcon: 'GITHUB',
        networkLabel: 'GitHub',
        networkUrl: 'https://github.com/test',
        networkPosition: 1,
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
    expect($result->networkId)->toBe('01H1234ABCD');
    expect($result->networkIcon)->toBe('GITHUB');
    expect($result->networkLabel)->toBe('GitHub');
    expect($result->networkUrl)->toBe('https://github.com/test');
    expect($result->networkPosition)->toBe(1);
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
        ->and($result->networkId)->toBe('')
        ->and($result->networkIcon)->toBe('')
        ->and($result->networkLabel)->toBe('GitHub')
        ->and($result->networkUrl)->toBe('')
        ->and($result->networkPosition)->toBe(0)
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
    expect($result->networkPosition)->toBe(2);
    expect($result->networkPosition)->toBeInt();
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
