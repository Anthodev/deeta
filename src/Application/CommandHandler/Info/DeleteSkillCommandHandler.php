<?php

declare(strict_types=1);

namespace App\Application\CommandHandler\Info;

use App\Application\Command\Info\DeleteSkillCommand;
use App\Domain\Repository\Info\SkillRepositoryInterface;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Ulid;

#[AsMessageHandler]
class DeleteSkillCommandHandler
{
    public function __construct(
        private readonly SkillRepositoryInterface $skillRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(DeleteSkillCommand $command): void
    {
        $skillId = Ulid::fromString($command->skillId);
        $userId = Ulid::fromString($command->userId);

        $skill = $this->skillRepository->find($skillId->toBase32());

        if (null === $skill) {
            throw new \RuntimeException('Skill not found');
        }

        $user = $this->userRepository->find($userId->toBase32());

        if (null === $user) {
            throw new \RuntimeException('User not found');
        }

        if ($skill->getUser()->getId() !== $user->getId()) {
            throw new \RuntimeException('User does not own this skill');
        }

        $this->skillRepository->delete($skill);
    }
}
