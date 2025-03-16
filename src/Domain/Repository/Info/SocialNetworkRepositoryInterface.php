<?php

declare(strict_types=1);

namespace App\Domain\Repository\Info;

use App\Domain\Model\Info\SocialNetwork;

/**
 * @method SocialNetwork|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocialNetwork[]    findAll()
 * @method SocialNetwork|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method SocialNetwork[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 * @method void               save(SocialNetwork $socialNetwork)
 * @method void               update(SocialNetwork $socialNetwork)
 * @method void               delete(SocialNetwork $socialNetwork)
 * @method void               refresh(SocialNetwork $socialNetwork)
 * @method void               rollback()
 */
interface SocialNetworkRepositoryInterface
{
    /**
     * @return array<SocialNetwork>
     */
    public function findNetworksByUserId(string $userId): array;
}
