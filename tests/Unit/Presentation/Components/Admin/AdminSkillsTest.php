<?php

declare(strict_types=1);

namespace App\Tests\Unit\Presentation\Components\Admin;

use App\Domain\Model\Info\Skill;
use App\Domain\Model\User\User;
use App\Presentation\Components\Admin\AdminSkills;
use App\Shared\Dto\Info\SkillDto;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

beforeEach(function () {
    /** @var MockObject&MessageBusInterface */
    $this->messageBus = $this->createMock(MessageBusInterface::class);

    /** @var MockObject&LoggerInterface */
    $this->logger = $this->createMock(LoggerInterface::class);

    $this->component = new AdminSkills(
        $this->messageBus,
        $this->logger
    );
});

it('initializes with empty errors array', function () {
    // Then
    expect($this->component->errors)->toBe([]);
});

it('mounts with correct data transformation', function () {
    // Given
    $userId = '01H1234ABCD';

    /** @var MockObject&User */
    $user = $this->createMock(User::class);
    $user->method('getId')->willReturn($userId);

    $skill1 = new Skill(
        label: 'PHP',
        defaultColor: '#777BB3',
        user: $user,
        position: 1,
    );

    $skill1Id = '01HSKILL01';
    $reflection = new \ReflectionClass($skill1);
    $idProperty = $reflection->getProperty('id');
    $idProperty->setAccessible(true);
    $idProperty->setValue($skill1, $skill1Id);

    $skill2 = new Skill(
        label: 'JavaScript',
        defaultColor: '#F7DF1E',
        user: $user,
        position: 2,
    );

    $skill2Id = '01HSKILL02';
    $reflection = new \ReflectionClass($skill2);
    $idProperty = $reflection->getProperty('id');
    $idProperty->setAccessible(true);
    $idProperty->setValue($skill2, $skill2Id);

    $skills = [$skill1, $skill2];

    // When
    $this->component->mount($userId, $skills);

    // Then
    expect($this->component->userId)
        ->toBe($userId)
        ->and($this->component->skills)->toBeArray()
        ->and(count($this->component->skills))->toBe(2)
        ->and($this->component->skills[0])->toBeInstanceOf(SkillDto::class)
        ->and($this->component->skills[0]->id)->toBe($skill1Id)
        ->and($this->component->skills[0]->label)->toBe('PHP')
        ->and($this->component->skills[0]->defaultColor)->toBe('#777BB3')
        ->and($this->component->skills[0]->position)->toBe(1)
        ->and($this->component->skills[0]->userId)->toBe($userId)
        ->and($this->component->skills[1])->toBeInstanceOf(SkillDto::class)
        ->and($this->component->skills[1]->id)->toBe($skill2Id)
        ->and($this->component->skills[1]->label)->toBe('JavaScript')
        ->and($this->component->skills[1]->defaultColor)->toBe('#F7DF1E')
        ->and($this->component->skills[1]->position)->toBe(2)
        ->and($this->component->skills[1]->userId)->toBe($userId);
});

it('validates form fields and sets errors', function () {
    // Given
    $this->component->userId = '01H1234ABCD';
    $this->component->skillLabel = '';
    $this->component->skillDefaultColor = '#000000';
    $this->component->skillPosition = 1;

    // When
    $this->component->save();

    // Then
    expect($this->component->errors)
        ->toHaveKey('skillLabel')
        ->and($this->component->errors['skillLabel'])->toBe('Veuillez saisir un label');
});

it('does not dispatch command with invalid form data', function () {
    // Given
    $this->component->userId = '01H1234ABCD';
    $this->component->skillLabel = '';

    // Expect
    $this->messageBus->expects($this->never())->method('dispatch');

    // When
    $this->component->save();
});
