<?php

declare(strict_types=1);

namespace App\Application\QueryHandler\User;

use App\Application\Query\User\GetRoleQuery;
use App\Domain\Model\User\Role;
use App\Domain\Repository\User\RoleRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetRoleQueryHandler
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }

    public function __invoke(GetRoleQuery $query): ?Role
    {
        return $this->roleRepository->findOneBy(['code' => $query->roleCode]);
    }
}
