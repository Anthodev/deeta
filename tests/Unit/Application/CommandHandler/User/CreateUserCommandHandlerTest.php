<?php

declare(strict_types=1);

use App\Application\Command\User\CreateUserCommand;
use App\Application\CommandHandler\User\CreateUserCommandHandler;
use App\Application\Query\User\GetRoleQuery;
use App\Domain\Model\User\Role;
use App\Domain\Model\User\User;
use App\Infrastructure\Enum\RoleCodeEnum;
use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineUserRepository;
use Faker\Factory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

beforeEach(function () {
    $this->faker = Factory::create();
    $this->userRepository = $this->createMock(DoctrineUserRepository::class);
    $this->messageBus = $this->createMock(MessageBusInterface::class);
    $this->logger = $this->createMock(LoggerInterface::class);

    $this->handler = new CreateUserCommandHandler(
        $this->userRepository,
        $this->messageBus,
        $this->logger,
    );
});

it('can create an user', function () {
    // Given
    $email = $this->faker->email();
    $username = $this->faker->userName();
    $password = $this->faker->password(12);
    $passwordConfirm = $password;

    $command = new CreateUserCommand(
        $email,
        $username,
        $password,
        $passwordConfirm
    );

    $role = new Role(RoleCodeEnum::ROLE_ADMIN->value);

    $envelope = new Envelope(new stdClass());
    $handledStamp = new HandledStamp($role, 'handler');
    $stampedEnvelope = $envelope->with($handledStamp);

    $this->messageBus
        ->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(function ($query) {
            return $query instanceof GetRoleQuery
                && $query->roleCode === RoleCodeEnum::ROLE_ADMIN->value;
        }))
        ->willReturn($stampedEnvelope);

    $this->userRepository
        ->expects($this->once())
        ->method('save')
        ->with($this->callback(function ($user) use ($email, $username, $password, $role) {
            return $user instanceof User
                && $user->getEmail() === $email
                && $user->getUsername() === $username
                && $user->getPlainPassword() === $password
                && 1 === $user->isEnabled()
                && $user->getRole() === $role;
        }));

    // When
    $this->handler->__invoke($command);
});

it('log an error when role retrieval fail', function () {
    // Given
    $email = $this->faker->email();
    $username = $this->faker->userName();
    $password = $this->faker->password(12);
    $passwordConfirm = $password;

    $command = new CreateUserCommand(
        $email,
        $username,
        $password,
        $passwordConfirm
    );

    $envelope = new Envelope(new stdClass());

    $this->messageBus
        ->expects($this->once())
        ->method('dispatch')
        ->willReturn($envelope);

    $this->logger
        ->expects($this->once())
        ->method('error')
        ->with('Role retrieval failed');

    $this->userRepository
        ->expects($this->once())
        ->method('save');

    // When
    $this->handler->__invoke($command);
});

it('handle a null Role result', function () {
    // Given
    $email = $this->faker->email();
    $username = $this->faker->userName();
    $password = $this->faker->password(12);
    $passwordConfirm = $password;

    $command = new CreateUserCommand(
        $email,
        $username,
        $password,
        $passwordConfirm
    );

    $envelope = new Envelope(new stdClass());
    $handledStamp = new HandledStamp(null, 'handler');
    $stampedEnvelope = $envelope->with($handledStamp);

    $this->messageBus
        ->expects($this->once())
        ->method('dispatch')
        ->willReturn($stampedEnvelope);

    $this->userRepository
        ->expects($this->once())
        ->method('save')
        ->with($this->callback(function ($user) use ($email, $username, $password) {
            return $user instanceof User
                && $user->getEmail() === $email
                && $user->getUsername() === $username
                && $user->getPlainPassword() === $password
                && 1 === $user->isEnabled()
                && null === $user->getRole();
        }));

    // When
    $this->handler->__invoke($command);
});
