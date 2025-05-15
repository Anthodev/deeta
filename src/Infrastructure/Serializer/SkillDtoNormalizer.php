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
            'id' => $object->skillId,
            'label' => $object->skillLabel,
            'position' => $object->skillPosition,
            'userId' => $object->skillUserId,
            'isTextWhite' => $object->skillIsTextWhite,
            'defaultColor' => $object->skillDefaultColor,
            '__type' => 'skill',
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
        $isTextWhite = isset($data['isTextWhite']) && (is_bool($data['isTextWhite']) || in_array($data['isTextWhite'], ['0', '1', 0, 1], true)) ? (bool) $data['isTextWhite'] : false;
        $defaultColor = isset($data['defaultColor']) && is_string($data['defaultColor']) ? $data['defaultColor'] : '';

        return new SkillDto(
            skillId: $id,
            skillLabel: $label,
            skillPosition: $position,
            skillUserId: $userId,
            skillIsTextWhite: $isTextWhite,
            skillDefaultColor: $defaultColor
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return SkillDto::class === $type || 'App\Shared\Dto\Info\SkillDto' === $type;
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
