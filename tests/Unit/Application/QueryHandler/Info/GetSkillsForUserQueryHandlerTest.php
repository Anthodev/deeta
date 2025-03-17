<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\QueryHandler\Info;

use App\Application\Query\Info\GetSkillsForUserQuery;
use App\Application\Query\Info\GetSkillsForUserResponse;
use App\Application\QueryHandler\Info\GetSkillsForUserQueryHandler;
use App\Domain\Model\Info\Skill;
use App\Domain\Model\User\User;
use App\Domain\Repository\Info\SkillRepositoryInterface;
use App\Shared\Dto\Info\SkillDto;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Ulid;

beforeEach(function () {
    $this->skillRepository = $this->createMock(SkillRepositoryInterface::class);
    $this->handler = new GetSkillsForUserQueryHandler(
        $this->skillRepository,
    );
});

it('returns skills for a user', function () {
    // Given
    $userId = (string) Ulid::generate();

    /** @var MockObject&User */
    $user = $this->createMock(User::class);
    $user->method('getId')->willReturn($userId);

    /** @var MockObject&Skill */
    $skill1 = $this->createMock(Skill::class);
    $skill1Id = (string) Ulid::generate();
    $skill1->method('getId')->willReturn($skill1Id);
    $skill1->method('getLabel')->willReturn('PHP');
    $skill1->method('getDefaultColor')->willReturn('#777BB3');
    $skill1->method('getPosition')->willReturn(1);
    $skill1->method('getUser')->willReturn($user);

    /** @var MockObject&Skill */
    $skill2 = $this->createMock(Skill::class);
    $skill2Id = (string) Ulid::generate();
    $skill2->method('getId')->willReturn($skill2Id);
    $skill2->method('getLabel')->willReturn('JavaScript');
    $skill2->method('getDefaultColor')->willReturn('#F7DF1E');
    $skill2->method('getPosition')->willReturn(2);
    $skill2->method('getUser')->willReturn($user);

    $skills = [$skill1, $skill2];

    $query = new GetSkillsForUserQuery($userId);

    $this->skillRepository
        ->expects($this->once())
        ->method('findSkillsByUserId')
        ->with($userId)
        ->willReturn($skills);

    // When
    $response = $this->handler->__invoke($query);

    // Then
    expect($response)
        ->toBeInstanceOf(GetSkillsForUserResponse::class)
        ->and($response->getContent())->toBeArray()
        ->and(count($response->getContent()))->toBe(2)
        ->and($response->getContent()[0])->toBeInstanceOf(SkillDto::class)
        ->and($response->getContent()[0]->id)->toBe($skill1Id)
        ->and($response->getContent()[0]->label)->toBe('PHP')
        ->and($response->getContent()[0]->defaultColor)->toBe('#777BB3')
        ->and($response->getContent()[0]->position)->toBe(1)
        ->and($response->getContent()[0]->userId)->toBe($userId)
        ->and($response->getContent()[1])->toBeInstanceOf(SkillDto::class)
        ->and($response->getContent()[1]->id)->toBe($skill2Id)
        ->and($response->getContent()[1]->label)->toBe('JavaScript')
        ->and($response->getContent()[1]->defaultColor)->toBe('#F7DF1E')
        ->and($response->getContent()[1]->position)->toBe(2)
        ->and($response->getContent()[1]->userId)->toBe($userId);
});

it('returns empty array when no skills found', function () {
    // Given
    $userId = (string) Ulid::generate();
    $query = new GetSkillsForUserQuery($userId);

    $this->skillRepository
        ->expects($this->once())
        ->method('findSkillsByUserId')
        ->with($userId)
        ->willReturn([]);

    // When
    $response = $this->handler->__invoke($query);

    // Then
    expect($response)
        ->toBeInstanceOf(GetSkillsForUserResponse::class)
        ->and($response->getContent())->toBeArray()
        ->and($response->getContent())->toBeEmpty();
});
