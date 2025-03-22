<?php

declare(strict_types=1);

namespace App\Application\Command\Info;

class DeleteSkillCommand
{
    public function __construct(
        public readonly string $skillId,
        public readonly string $userId,
    ) {
    }
}
