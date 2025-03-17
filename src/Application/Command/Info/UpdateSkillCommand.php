<?php

declare(strict_types=1);

namespace App\Application\Command\Info;

class UpdateSkillCommand
{
    public function __construct(
        public readonly string $skillId,
        public readonly string $userId,
        public readonly string $label,
        public readonly ?string $defaultColor,
        public readonly int $position,
    ) {
    }
}
