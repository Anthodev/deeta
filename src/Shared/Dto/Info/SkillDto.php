<?php

declare(strict_types=1);

namespace App\Shared\Dto\Info;

use Symfony\Component\Serializer\Attribute\Groups;

#[Groups(['skill'])]
readonly class SkillDto
{
    public function __construct(
        public string $skillId,
        public string $skillLabel,
        public int $skillPosition,
        public string $skillUserId,
        public bool $skillIsTextWhite = false,
        public string $skillDefaultColor = '',
        public string $__dto_type = 'skill',
    ) {
    }
}
