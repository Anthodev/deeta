<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Entity\Trait\CodeTrait;
use App\Application\Common\Entity\Trait\IdTrait;
use App\Application\Common\Entity\Trait\LabelTrait;
use App\Application\Common\Entity\Trait\TimestampableTrait;
use App\Infrastructure\Persistence\Doctrine\User\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: 'roles')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['code'], message: 'This code is already used.')]
#[UniqueEntity(fields: ['label'], message: 'This label is already used.')]
class Role implements EntityInterface
{
    use IdTrait;
    use LabelTrait;
    use CodeTrait;
    use TimestampableTrait;
}
