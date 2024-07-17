<?php

declare(strict_types=1);

namespace App\Application\Common\Helper;

use App\Application\Common\Entity\EntityInterface;

class EntityInterfaceHelper
{
    public static function areTheSame(EntityInterface $entity, EntityInterface $entityToCompareWith): bool
    {
        if (null === $entity->getId()) {
            return false;
        }

        return $entity->getId()->equals($entityToCompareWith->getId());
    }
}
