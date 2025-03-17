<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use App\Shared\Dto\Info\SkillDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SkillDtoNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        if (!$object instanceof SkillDto) {
            throw new \InvalidArgumentException('The object must be an instance of SkillDto');
        }

        return [
            'id' => $object->id,
            'label' => $object->label,
            'position' => $object->position,
            'userId' => $object->userId,
            'defaultColor' => $object->defaultColor,
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof SkillDto;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): SkillDto
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Data is not an array');
        }

        $id = isset($data['id']) && is_string($data['id']) ? $data['id'] : '';
        $label = isset($data['label']) && is_string($data['label']) ? $data['label'] : '';
        $position = isset($data['position']) && (is_int($data['position']) || is_string($data['position'])) ? (int) $data['position'] : 0;
        $userId = isset($data['userId']) && is_string($data['userId']) ? $data['userId'] : '';
        $defaultColor = isset($data['defaultColor']) && is_string($data['defaultColor']) ? $data['defaultColor'] : '';

        return new SkillDto(
            id: $id,
            label: $label,
            position: $position,
            userId: $userId,
            defaultColor: $defaultColor,
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return SkillDto::class === $type || 'App\Shared\Dto\Info\SkillDto' === $type && is_array($data);
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            SkillDto::class => true,
        ];
    }
}
