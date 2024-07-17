<?php

declare(strict_types=1);

namespace App\Domain\User\Fetcher;

use App\Application\Common\Fetcher\AbstractFetcherInterface;
use App\Domain\User\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserFetcherInterface extends AbstractFetcherInterface
{
    public function findOneByEmailOrUsername(string $email, string $username): ?User;

    /**
     * @return User[]
     */
    public function getAllEnabled(): array;

    /**
     * @throws NonUniqueResultException
     */
    public function getOneByIdEnabled(string $id): ?User;

    public function loadUserByIdentifier(string $identifier): ?UserInterface;
}
