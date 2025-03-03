<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Common\ModelInterface;

interface BaseRepositoryInterface
{
    public function find(string $id): ?ModelInterface;

    /** @param array<string, mixed> $criteria */
    public function findOneBy(array $criteria): ?ModelInterface;

    /**
     * @param array<string, mixed>      $criteria
     * @param array<string, mixed>|null $orderBy
     *
     * @return array<ModelInterface>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /** @return array<ModelInterface> */
    public function findAll(): array;

    public function save(ModelInterface $entity): void;

    public function update(ModelInterface $entity): void;

    public function delete(ModelInterface $entity): void;

    public function refresh(ModelInterface $entity): void;

    public function rollback(): void;
}
