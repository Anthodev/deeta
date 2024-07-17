<?php

declare(strict_types=1);

namespace App\Application\Common\Fetcher;

use App\Application\Common\Entity\EntityInterface;
use App\Infrastructure\Persistence\Doctrine\Common\BaseEntityRepository;

abstract class AbstractFetcher implements AbstractFetcherInterface
{
    public function __construct(
        protected readonly BaseEntityRepository $repository,
    ) {
    }

    public function update(EntityInterface $entity): void
    {
        $this->repository->update($entity);
    }

    public function save(EntityInterface $entity): void
    {
        $this->repository->save($entity);
    }

    public function delete(EntityInterface $entity): void
    {
        $this->repository->delete($entity);
    }

    public function find(string $id, ?int $lockMode = null, ?int $lockVersion = null): ?EntityInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->repository->find($id, $lockMode, $lockVersion);
    }

    /**
     * @param array<string, mixed>       $criteria
     * @param array<string, string>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?EntityInterface
    {
        /** @var ?EntityInterface */
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    public function findAll(): array
    {
        /** @var EntityInterface[] */
        return $this->repository->findAll();
    }

    /**
     * @param array<string, mixed>       $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return EntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        /** @var EntityInterface[] */
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }
}
