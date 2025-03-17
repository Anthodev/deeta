<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\CommandHandler\Info;

use App\Application\Command\Info\DeleteSkillCommand;
use App\Application\CommandHandler\Info\DeleteSkillCommandHandler;
use App\Domain\Model\Info\Skill;
use App\Domain\Model\User\User;
use App\Infrastructure\Persistence\Doctrine\Info\Repository\DoctrineSkillRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineUserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Ulid;

beforeEach(function () {
    $this->userRepository = $this->createMock(DoctrineUserRepository::class);
    $this->skillRepository = $this->createMock(DoctrineSkillRepository::class);
    $this->handler = new DeleteSkillCommandHandler(
        $this->skillRepository,
        $this->userRepository,
    );
});

it('can delete a skill', function () {
    // Given
    /** @var MockObject&User */
    $user = $this->createMock(User::class);
    $userId = (string) Ulid::generate();
    $user->method('getId')->willReturn($userId);

    /** @var MockObject&Skill */
    $skill = $this->createMock(Skill::class);
    $skillId = (string) Ulid::generate();

    $skill->method('getUser')->willReturn($user);

    $command = new DeleteSkillCommand(
        $skillId,
        $userId
    );

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with(Ulid::fromString($userId)->toBase32())
        ->willReturn($user);

    $this->skillRepository
        ->expects($this->once())
        ->method('find')
        ->with(Ulid::fromString($skillId)->toBase32())
        ->willReturn($skill);

    $this->skillRepository
        ->expects($this->once())
        ->method('delete')
        ->with($skill);

    // When
    $this->handler->__invoke($command);
});

it('cannot delete a skill that does not exist', function () {
    // Given
    /** @var MockObject&User */
    $user = $this->createMock(User::class);
    $userId = (string) Ulid::generate();
    $user->method('getId')->willReturn($userId);

    $skillId = (string) Ulid::generate();

    $command = new DeleteSkillCommand(
        $skillId,
        $userId
    );

    $this->skillRepository
        ->expects($this->once())
        ->method('find')
        ->with(Ulid::fromString($skillId)->toBase32())
        ->willReturn(null);

    $this->userRepository
        ->expects($this->never())
        ->method('find')
        ->with(Ulid::fromString($userId)->toBase32())
        ->willReturn($user);

    $this->skillRepository
        ->expects($this->never())
        ->method('delete');

    // When & Then
    $this->handler->__invoke($command);
})->throws(\RuntimeException::class, 'Skill not found');

it('cannot delete a skill for a user that does not exist', function () {
    // Given
    $userId = (string) Ulid::generate();
    $skillId = (string) Ulid::generate();

    $command = new DeleteSkillCommand(
        $skillId,
        $userId
    );

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with(Ulid::fromString($userId)->toBase32())
        ->willReturn(null);

    $this->skillRepository
        ->expects($this->once())
        ->method('find')
        ->with(Ulid::fromString($skillId)->toBase32())
        ->willReturn($this->createMock(Skill::class));

    $this->skillRepository
        ->expects($this->never())
        ->method('delete');

    // When & Then
    $this->handler->__invoke($command);
})->throws(\RuntimeException::class, 'User not found');

it('cannot delete a skill owned by another user', function () {
    // Given
    /** @var MockObject&User */
    $user = $this->createMock(User::class);
    $userId = (string) Ulid::generate();
    $user->method('getId')->willReturn($userId);

    /** @var MockObject&User */
    $anotherUser = $this->createMock(User::class);
    $anotherUserId = (string) Ulid::generate();
    $anotherUser->method('getId')->willReturn($anotherUserId);

    /** @var MockObject&Skill */
    $skill = $this->createMock(Skill::class);
    $skillId = (string) Ulid::generate();

    $command = new DeleteSkillCommand(
        $skillId,
        $userId
    );

    $skill->method('getUser')->willReturn($anotherUser);

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with(Ulid::fromString($userId)->toBase32())
        ->willReturn($user);

    $this->skillRepository
        ->expects($this->once())
        ->method('find')
        ->with(Ulid::fromString($skillId)->toBase32())
        ->willReturn($skill);

    $this->skillRepository
        ->expects($this->never())
        ->method('delete');

    // When & Then
    $this->handler->__invoke($command);
})->throws(\RuntimeException::class, 'User does not own this skill');
