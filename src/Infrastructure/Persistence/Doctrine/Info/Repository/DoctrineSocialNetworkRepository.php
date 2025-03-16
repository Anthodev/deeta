<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Info\Repository;

use App\Domain\Model\Info\SocialNetwork;
use App\Domain\Repository\Info\SocialNetworkRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\DoctrineBaseEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineSocialNetworkRepository extends DoctrineBaseEntityRepository implements SocialNetworkRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialNetwork::class);
    }

    /**
     * @return array<SocialNetwork>
     */
    public function findNetworksByUserId(string $userId): array
    {
        /** @var array<SocialNetwork> */
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
