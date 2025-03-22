<?php

declare(strict_types=1);

namespace App\Shared\Dto\Info;

use Symfony\Component\Serializer\Attribute\Groups;

#[Groups(['skill'])]
readonly class SkillDto
{
    public function __construct(
        #[Groups(['skill'])]
        public string $id,

        #[Groups(['skill'])]
        public string $label,

        #[Groups(['skill'])]
        public int $position,

        #[Groups(['skill'])]
        public string $userId,

        #[Groups(['skill'])]
        public string $defaultColor = '',
    ) {
    }
}
