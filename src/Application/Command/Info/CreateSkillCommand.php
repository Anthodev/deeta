<?php

declare(strict_types=1);

namespace App\Application\Command\Info;

class CreateSkillCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly string $label,
        public readonly int $position,
        public readonly string $defaultColor = '',
    ) {
    }
}
