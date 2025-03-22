<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\CommandHandler\Info;

use App\Application\Command\Info\CreateSkillCommand;
use App\Application\CommandHandler\Info\CreateSkillCommandHandler;
use App\Domain\Model\Info\Skill;
use App\Domain\Model\User\User;
use App\Infrastructure\Persistence\Doctrine\Info\Repository\DoctrineSkillRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineUserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Ulid;

beforeEach(function () {
    $this->userRepository = $this->createMock(DoctrineUserRepository::class);
    $this->skillRepository = $this->createMock(DoctrineSkillRepository::class);
    $this->handler = new CreateSkillCommandHandler(
        $this->userRepository,
        $this->skillRepository,
    );
});

it('can create a skill', function () {
    // Given
    /** @var MockObject&User */
    $user = $this->createMock(User::class);
    $userId = (string) Ulid::generate();
    $user->method('getId')->willReturn($userId);

    $command = new CreateSkillCommand(
        userId: $userId,
        label: 'PHP',
        defaultColor: '#777BB3',
        position: 1,
    );

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with(Ulid::fromString($userId)->toBase32())
        ->willReturn($user);

    $this->skillRepository
        ->expects($this->once())
        ->method('save')
        ->with($this->callback(function (Skill $skill) use ($user, $command) {
            return $skill->getLabel() === $command->label
                && $skill->getDefaultColor() === $command->defaultColor
                && $skill->getUser() === $user
                && $skill->getPosition() === $command->position;
        }));

    // When
    $this->handler->__invoke($command);
});

it('cannot create a skill for a user that does not exist', function () {
    // Given
    $userId = (string) Ulid::generate();
    $command = new CreateSkillCommand(
        userId: $userId,
        label: 'PHP',
        defaultColor: '#777BB3',
        position: 1,
    );

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with(Ulid::fromString($userId)->toBase32())
        ->willReturn(null);

    $this->skillRepository
        ->expects($this->never())
        ->method('save');

    // When & Then
    $this->handler->__invoke($command);
})->throws(\RuntimeException::class, 'User not found');
