<?php

declare(strict_types=1);

namespace App\Application\Query\Info;

use App\Shared\Dto\Info\SkillDto;

class GetSkillsForUserResponse
{
    /**
     * @param array<int, SkillDto> $content
     */
    public function __construct(
        private readonly array $content = [],
    ) {
    }

    /**
     * @return array<int, SkillDto>
     */
    public function getContent(): array
    {
        return $this->content;
    }
}
