<?php

declare(strict_types=1);

namespace App\Application\QueryHandler\Info;

use App\Application\Query\Info\GetSkillsForUserQuery;
use App\Application\Query\Info\GetSkillsForUserResponse;
use App\Domain\Model\Info\Skill;
use App\Domain\Repository\Info\SkillRepositoryInterface;
use App\Shared\Dto\Info\SkillDto;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetSkillsForUserQueryHandler
{
    public function __construct(
        private readonly SkillRepositoryInterface $skillRepository,
    ) {
    }

    public function __invoke(GetSkillsForUserQuery $query): GetSkillsForUserResponse
    {
        $skills = $this->skillRepository->findSkillsByUserId($query->userId);

        $skillDtos = [];

        /** @var Skill $skill */
        foreach ($skills as $skill) {
            $skillDtos[] = new SkillDto(
                skillId: $skill->getId() ?? '',
                skillLabel: $skill->getLabel(),
                skillPosition: $skill->getPosition(),
                skillUserId: $skill->getUser()->getId() ?? '',
                skillIsTextWhite: $skill->getIsTextWhite(),
                skillDefaultColor: $skill->getDefaultColor() ?? '',
            );
        }

        return new GetSkillsForUserResponse($skillDtos);
    }
}
