<?php

namespace App\Application\Common\Provider;

use App\Domain\User\Entity\Role;
use App\Domain\User\Entity\User;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

readonly class ContextProvider
{
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
    ) {
    }

    public function getContextUser(): User
    {
        $user = $this->security->getUser();
        \assert($user instanceof User);

        $user = $this->userRepository->find($user->getId());
        \assert($user instanceof User);

        return $user;
    }

    public function getContextUserRoleCode(): string
    {
        $user = $this->getContextUser();

        $role = $user->getRole();
        \assert($role instanceof Role);

        /** @var string $roleCode */
        $roleCode = $role->getCode();

        return $roleCode;
    }
}
