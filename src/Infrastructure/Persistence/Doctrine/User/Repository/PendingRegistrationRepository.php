<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\User\Repository;

use App\Application\Common\Exception\EntityNotFoundException;
use App\Domain\User\Entity\PendingRegistration;
use App\Infrastructure\Persistence\Doctrine\Common\BaseEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class PendingRegistrationRepository extends BaseEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PendingRegistration::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByEmailOrUsername(string $email, string $username): ?PendingRegistration
    {
        $query = $this->createQueryBuilder('pr')
            ->where('pr.email = :email')
            ->orWhere('pr.username = :username')
            ->setParameter('email', $email)
            ->setParameter('username', $username)
            ->getQuery();

        /** @var PendingRegistration|null */
        return $query->getOneOrNullResult();
    }

    /**
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     */
    public function findOneByTokenOrFail(string $token): PendingRegistration
    {
        $query = $this->createQueryBuilder('pr')
            ->where('pr.token = :token')
            ->setParameter('token', $token)
            ->getQuery();

        /** @var PendingRegistration|null $pendingRegistration */
        $pendingRegistration = $query->getOneOrNullResult();

        if (null === $pendingRegistration) {
            throw new EntityNotFoundException();
        }

        return $pendingRegistration;
    }
}
