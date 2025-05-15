<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use App\Shared\Dto\Info\SocialNetworkDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SocialNetworkDtoNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        if (!$object instanceof SocialNetworkDto) {
            throw new \InvalidArgumentException('The object must be an instance of SocialNetworkDto');
        }

        return [
            'id' => $object->networkId,
            'network' => $object->networkIcon,
            'label' => $object->networkLabel,
            'url' => $object->networkUrl,
            'position' => $object->networkPosition,
            'userId' => $object->userId,
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof SocialNetworkDto;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): SocialNetworkDto
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Data is not an array');
        }

        $id = isset($data['id']) && is_string($data['id']) ? $data['id'] : '';
        $network = isset($data['network']) && is_string($data['network']) ? $data['network'] : '';
        $label = isset($data['label']) && is_string($data['label']) ? $data['label'] : '';
        $url = isset($data['url']) && is_string($data['url']) ? $data['url'] : '';
        $position = isset($data['position']) && (is_int($data['position']) || is_string($data['position'])) ? (int) $data['position'] : 0;
        $userId = isset($data['userId']) && is_string($data['userId']) ? $data['userId'] : '';

        return new SocialNetworkDto(
            networkId: $id,
            networkIcon: $network,
            networkLabel: $label,
            networkUrl: $url,
            networkPosition: $position,
            userId: $userId,
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return SocialNetworkDto::class === $type || 'App\Shared\Dto\Info\SocialNetworkDto' === $type;
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            SocialNetworkDto::class => true,
        ];
    }
}
