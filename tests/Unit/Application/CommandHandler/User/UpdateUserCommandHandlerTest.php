<?php

declare(strict_types=1);

use App\Application\Command\User\UpdateUserCommand;
use App\Application\CommandHandler\User\UpdateUserCommandHandler;
use App\Domain\Model\User\User;
use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineUserRepository;

beforeEach(function () {
    $this->userRepository = $this->createMock(DoctrineUserRepository::class);
    $this->handler = new UpdateUserCommandHandler(
        $this->userRepository,
    );
});

it('can update a user', function () {
    // Given
    /** @var User $user */
    $user = $this->makeMockedUser();

    $userId = $user->getId();
    $command = new UpdateUserCommand(
        $userId,
        'John',
        'Doe',
        'Developer',
        'ACME Inc',
        'New York'
    );

    $userUpdated = $user;
    $userUpdated->setFirstName('John');
    $userUpdated->setLastName('Doe');
    $userUpdated->setJobTitle('Developer');
    $userUpdated->setCompany('ACME Inc');
    $userUpdated->setLocation('New York');

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with($userId)
        ->willReturn($user);

    $this->userRepository
        ->expects($this->once())
        ->method('save')
        ->with($userUpdated);

    // When
    $this->handler->__invoke($command);
});

it('cannot update a user that does not exist', function () {
    // Given
    $userId = 'non-existent-id';
    $command = new UpdateUserCommand($userId);

    $this->userRepository
        ->expects($this->once())
        ->method('find');

    $this->userRepository
        ->expects($this->never())
        ->method('save');

    // When & Then
    $this->handler->__invoke($command);
})->throws(RuntimeException::class, 'User not found');
