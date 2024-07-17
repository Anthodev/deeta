<?php

declare(strict_types=1);

namespace App\Application\Common\Fetcher;

use App\Application\Common\Entity\EntityInterface;

interface AbstractFetcherInterface
{
    public function update(EntityInterface $entity): void;

    public function save(EntityInterface $entity): void;

    public function delete(EntityInterface $entity): void;

    public function find(string $id, ?int $lockMode = null, ?int $lockVersion = null): ?EntityInterface;

    /**
     * @param array<string, mixed>       $criteria
     * @param array<string, string>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?EntityInterface;

    /**
     * @return EntityInterface[]
     */
    public function findAll(): array;

    /**
     * @param array<string, mixed>       $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return EntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
}
