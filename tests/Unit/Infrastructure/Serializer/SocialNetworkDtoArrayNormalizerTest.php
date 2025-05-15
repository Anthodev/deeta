<?php

declare(strict_types=1);

use App\Infrastructure\Serializer\SocialNetworkDtoArrayNormalizer;
use App\Shared\Dto\Info\SocialNetworkDto;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

beforeEach(function () {
    /** @var MockObject&NormalizerInterface */
    $this->normalizer = $this->createMock(NormalizerInterface::class);
    /** @var MockObject&DenormalizerInterface */
    $this->denormalizer = $this->createMock(DenormalizerInterface::class);

    $this->socialNetworkDtoArrayNormalizer = new SocialNetworkDtoArrayNormalizer();
    $this->socialNetworkDtoArrayNormalizer->setNormalizer($this->normalizer);
    $this->socialNetworkDtoArrayNormalizer->setDenormalizer($this->denormalizer);
});

it('normalizes an array of SocialNetworkDto objects', function () {
    // Given
    $dto1 = new SocialNetworkDto(
        networkId: '01H1234ABCD',
        networkIcon: 'GITHUB',
        networkLabel: 'GitHub',
        networkUrl: 'https://github.com/test',
        networkPosition: 1,
        userId: '01H1234ABCD',
    );

    $dto2 = new SocialNetworkDto(
        networkId: '01H5678EFGH',
        networkIcon: 'LINKEDIN',
        networkLabel: 'LinkedIn',
        networkUrl: 'https://linkedin.com/in/test',
        networkPosition: 2,
        userId: '01H1234ABCD',
    );

    $expectedNormalizedDto1 = ['id' => '01H1234ABCD', 'label' => 'GitHub', 'network' => 'GITHUB', 'url' => 'https://github.com/test'];
    $expectedNormalizedDto2 = ['id' => '01H5678EFGH', 'label' => 'LinkedIn', 'network' => 'LINKEDIN', 'url' => 'https://linkedin.com/in/test'];

    $dtos = [$dto1, $dto2];
    $format = 'json';

    $matcher = $this->exactly(2);
    $this->normalizer
        ->expects($matcher)
        ->method('normalize')
        ->willReturnCallback(function ($dto) use ($matcher, $expectedNormalizedDto1, $expectedNormalizedDto2) {
            return match ($matcher->numberOfInvocations()) {
                1 => $expectedNormalizedDto1,
                2 => $expectedNormalizedDto2,
            };
        });

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->normalize($dtos, $format);

    // Then
    expect($result)->toBe([$expectedNormalizedDto1, $expectedNormalizedDto2]);
});

it('throws an exception when normalizing non-array data', function () {
    // Given
    $invalidData = 'not an array';

    // When & Then
    $this->socialNetworkDtoArrayNormalizer->normalize($invalidData);
})->throws(InvalidArgumentException::class, 'The object must be an array');

it('supports normalization of an array of SocialNetworkDto objects', function () {
    // Given
    /** @var SocialNetworkDto&MockObject */
    $dto1 = $this->createMock(SocialNetworkDto::class);
    /** @var SocialNetworkDto&MockObject */
    $dto2 = $this->createMock(SocialNetworkDto::class);

    $dtos = [$dto1, $dto2];

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->supportsNormalization($dtos);

    // Then
    expect($result)->toBeTrue();
});

it('does not support normalization of empty arrays', function () {
    // Given
    $emptyArray = [];

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->supportsNormalization($emptyArray);

    // Then
    expect($result)->toBeFalse();
});

it('does not support normalization of non-array data', function () {
    // Given
    $nonArrayData = 'string';

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->supportsNormalization($nonArrayData);

    // Then
    expect($result)->toBeFalse();
});

it('does not support normalization of arrays with non-SocialNetworkDto items', function () {
    // Given
    /** @var SocialNetworkDto&MockObject */
    $dto = $this->createMock(SocialNetworkDto::class);
    $mixedArray = [$dto, 'string', 123];

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->supportsNormalization($mixedArray);

    // Then
    expect($result)->toBeFalse();
});

it('denormalizes an array of data to SocialNetworkDto objects', function () {
    // Given
    $data1 = ['id' => '01H1234ABCD', 'label' => 'GitHub', 'network' => 'GITHUB', 'url' => 'https://github.com/test'];
    $data2 = ['id' => '01H5678EFGH', 'label' => 'LinkedIn', 'network' => 'LINKEDIN', 'url' => 'https://linkedin.com/in/test'];

    $expectedDto1 = new SocialNetworkDto(
        networkId: $data1['id'],
        networkIcon: $data1['network'],
        networkLabel: $data1['label'],
        networkUrl: $data1['url'],
        networkPosition: 0,
        userId: '01H1234ABCD',
    );

    $expectedDto2 = new SocialNetworkDto(
        networkId: $data2['id'],
        networkIcon: $data2['network'],
        networkLabel: $data2['label'],
        networkUrl: $data2['url'],
        networkPosition: 0,
        userId: '01H5678EFGH',
    );

    $data = [$data1, $data2];
    $format = 'json';

    $matcher = $this->exactly(2);
    $this->denormalizer
        ->expects($matcher)
        ->method('denormalize')
        ->willReturnCallback(function ($item, $type) use ($matcher, $expectedDto1, $expectedDto2) {
            return match ($matcher->numberOfInvocations()) {
                1 => $expectedDto1,
                2 => $expectedDto2,
            };
        });

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->denormalize($data, 'array', $format);

    // Then
    expect($result)->toBe([$expectedDto1, $expectedDto2]);
});

it('returns empty array when denormalizing non-array data', function () {
    // Given
    $invalidData = 'not an array';

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->denormalize($invalidData, 'array');

    // Then
    expect($result)->toBe([]);
});

it('supports denormalization to array type', function () {
    // Given
    $data = [['label' => 'GitHub']];

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->supportsDenormalization($data, 'array');

    // Then
    expect($result)->toBeTrue();
});

it('supports denormalization to array<SocialNetworkDto> type', function () {
    // Given
    $data = [['label' => 'GitHub']];

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->supportsDenormalization($data, 'array<SocialNetworkDto>');

    // Then
    expect($result)->toBeTrue();
});

it('does not support denormalization to non-array types', function () {
    // Given
    $data = [['label' => 'GitHub']];

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->supportsDenormalization($data, 'string');

    // Then
    expect($result)->toBeFalse();
});

it('does not support denormalization of non-array data', function () {
    // Given
    $nonArrayData = 'string';

    // When
    $result = $this->socialNetworkDtoArrayNormalizer->supportsDenormalization($nonArrayData, 'array');

    // Then
    expect($result)->toBeFalse();
});

it('returns correct supported types', function () {
    // When
    $result = $this->socialNetworkDtoArrayNormalizer->getSupportedTypes(null);

    // Then
    expect($result)->toBe(['array' => true]);
});
