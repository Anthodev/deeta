<?php

declare(strict_types=1);

namespace App\Domain\User\Fetcher;

use App\Application\Common\Fetcher\AbstractFetcher;
use App\Domain\User\Entity\User;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserFetcher extends AbstractFetcher implements UserFetcherInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($userRepository);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByEmailOrUsername(string $email, string $username): ?User
    {
        return $this->userRepository->findOneByEmailOrUsername($email, $username);
    }

    public function getAllEnabled(): array
    {
        return $this->userRepository->getAllEnabled();
    }

    public function getOneByIdEnabled(string $id): ?User
    {
        return $this->userRepository->getOneByIdEnabled($id);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->userRepository->loadUserByIdentifier($identifier);
    }
}
