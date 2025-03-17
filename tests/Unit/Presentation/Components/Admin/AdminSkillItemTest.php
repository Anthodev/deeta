<?php

declare(strict_types=1);

namespace App\Tests\Unit\Presentation\Components\Admin;

use App\Presentation\Components\Admin\AdminSkillItem;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

beforeEach(function () {
    /** @var MockObject&MessageBusInterface */
    $this->messageBus = $this->createMock(MessageBusInterface::class);

    /** @var MockObject&LoggerInterface */
    $this->logger = $this->createMock(LoggerInterface::class);

    $this->component = new AdminSkillItem(
        $this->messageBus,
        $this->logger
    );
});

it('initializes correctly with mount method', function () {
    // Given
    $label = 'PHP';
    $defaultColor = '#777BB3';
    $position = 1;
    $skillId = '01HSKILL01';
    $userId = '01H1234ABCD';
    $componentId = 'skill-01HSKILL01';

    // When
    $this->component->mount(
        $label,
        $defaultColor,
        $position,
        $skillId,
        $userId,
        $componentId
    );

    // Then
    expect($this->component->label)
        ->toBe($label)
        ->and($this->component->defaultColor)->toBe($defaultColor)
        ->and($this->component->position)->toBe((string) $position)
        ->and($this->component->skillId)->toBe($skillId)
        ->and($this->component->userId)->toBe($userId)
        ->and($this->component->componentId)->toBe($componentId);
});
