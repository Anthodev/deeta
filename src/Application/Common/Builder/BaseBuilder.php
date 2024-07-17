<?php

declare(strict_types=1);

namespace App\Application\Common\Builder;

use App\Application\Common\Dto\InputDtoInterface;
use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Exception\BuildException;
use Symfony\Component\Uid\Ulid;

abstract class BaseBuilder
{
    /**
     * @throws BuildException
     */
    public function populate(
        InputDtoInterface $sourceObject,
        EntityInterface $targetObject,
    ): EntityInterface {
        $sourceReflection = new \ReflectionObject($sourceObject);
        $targetReflection = new \ReflectionObject($targetObject);

        foreach ($sourceReflection->getProperties() as $property) {
            $propertyName = $property->getName();

            if ($targetReflection->hasProperty($propertyName)) {
                $targetProperty = $targetReflection->getProperty($propertyName);

                $sourceProperty = $sourceReflection->getProperty($propertyName);

                $targetValue = $targetProperty->getValue($targetObject);
                $sourceValue = $sourceProperty->getValue($sourceObject);

                $isTargetPropertyNullable = $targetProperty->getType()?->allowsNull();

                if (false === $isTargetPropertyNullable && null === $sourceValue) {
                    continue;
                }

                if ($targetValue instanceof Ulid) {
                    /** @var string $originalSourceValue */
                    $originalSourceValue = $sourceValue;

                    $sourceValue = Ulid::fromString($originalSourceValue);
                }

                if ($targetValue instanceof \DateTimeInterface) {
                    /** @var string $originalSourceValue */
                    $originalSourceValue = $sourceValue;

                    $sourceValue = new \DateTime($originalSourceValue);
                }

                $targetProperty->setValue($targetObject, $sourceValue);
            }
        }

        return $targetObject;
    }
}
