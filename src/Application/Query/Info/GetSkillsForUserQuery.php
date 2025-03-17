<?php

declare(strict_types=1);

namespace App\Application\Query\Info;

class GetSkillsForUserQuery
{
    public function __construct(
        public readonly string $userId,
    ) {
    }
}
