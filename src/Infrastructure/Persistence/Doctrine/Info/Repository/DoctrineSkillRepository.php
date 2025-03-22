<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Info\Repository;

use App\Domain\Model\Info\Skill;
use App\Domain\Repository\Info\SkillRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\DoctrineBaseEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineSkillRepository extends DoctrineBaseEntityRepository implements SkillRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    /**
     * @return array<Skill>
     */
    public function findSkillsByUserId(string $userId): array
    {
        /** @var array<Skill> */
        return $this->findBy(
            [
                'user' => $userId,
            ],
            [
                'position' => 'ASC',
            ]
        );
    }
}
