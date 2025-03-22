<?php

declare(strict_types=1);

namespace App\Domain\Repository\Info;

use App\Domain\Model\Info\Skill;

/**
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill[]    findAll()
 * @method Skill|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Skill[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 * @method void       save(Skill $skill)
 * @method void       update(Skill $skill)
 * @method void       delete(Skill $skill)
 * @method void       refresh(Skill $skill)
 * @method void       rollback()
 */
interface SkillRepositoryInterface
{
    /**
     * @return array<Skill>
     */
    public function findSkillsByUserId(string $userId): array;
}
