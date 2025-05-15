<?php

declare(strict_types=1);

namespace App\Application\CommandHandler\Info;

use App\Application\Command\Info\CreateSkillCommand;
use App\Domain\Model\Info\Skill;
use App\Domain\Repository\Info\SkillRepositoryInterface;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Ulid;

#[AsMessageHandler]
class CreateSkillCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SkillRepositoryInterface $skillRepository,
    ) {
    }

    public function __invoke(CreateSkillCommand $command): void
    {
        $userId = Ulid::fromString($command->userId);
        $user = $this->userRepository->find($userId->toBase32());

        if (null === $user) {
            throw new \RuntimeException('User not found');
        }

        $skill = new Skill(
            label: $command->label,
            user: $user,
            isTextWhite: $command->isTextWhite,
            defaultColor: $command->defaultColor,
            position: $command->position,
        );

        $this->skillRepository->save($skill);
    }
}
