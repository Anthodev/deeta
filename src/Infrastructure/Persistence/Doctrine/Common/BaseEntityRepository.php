<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Common;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Validator\ValidatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Contracts\Service\Attribute\Required;

abstract class BaseEntityRepository extends ServiceEntityRepository
{
    #[Required]
    public ValidatorService $validatorService;

    public function update(EntityInterface $entity): void
    {
        $this->validatorService->validate($entity);
        $this->getEntityManager()->flush();
    }

    public function save(EntityInterface $entity): void
    {
        $this->validatorService->validate($entity);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function delete(EntityInterface $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function startTransaction(): void
    {
        $this->getEntityManager()->beginTransaction();
    }

    public function endTransaction(): void
    {
        $this->getEntityManager()->commit();
    }

    public function rollback(): void
    {
        $this->getEntityManager()->rollback();
    }
}
