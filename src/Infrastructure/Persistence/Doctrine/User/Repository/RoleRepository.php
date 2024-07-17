<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\User\Repository;

use App\Domain\User\Entity\Role;
use App\Infrastructure\Persistence\Doctrine\Common\BaseEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleRepository extends BaseEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }
}
