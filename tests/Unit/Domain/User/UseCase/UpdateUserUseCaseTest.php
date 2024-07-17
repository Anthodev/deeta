<?php

namespace App\Tests\Unit\Domain\User\UseCase;

use App\Domain\User\Builder\UserBuilder;
use App\Domain\User\Dto\UpdateUserInputDto;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Fetcher\UserFetcherInterface;
use App\Domain\User\UseCase\UpdateUserUseCase;
use DateTimeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use function Pest\Faker\fake;

beforeEach(function () {
    $this->userFetcher = $this->getContainer()->get(UserFetcherInterface::class);
    $this->userBuilder = $this->getContainer()->get(UserBuilder::class);
    $this->eventDispatcher = $this->getContainer()->get(EventDispatcherInterface::class);
});

it('can update an user', function () {
    // Given
    $user = $this->makeUser();

    $updateUserInput = new UpdateUserInputDto(
        email: fake()->email(),
        username: fake()->userName(),
    );

    $updateUserUseCase = new UpdateUserUseCase(
        $this->userFetcher,
        $this->userBuilder,
        $this->eventDispatcher,
    );

    // When
    $updateUserUseCase->updateUser($updateUserInput, $user);

    // Then
    $updatedUser = $this->userFetcher->find($user->getId());

    expect($updatedUser)
        ->toBeInstanceOf(User::class)
        ->getId()
            ->toBe($user->getId())
        ->getEmail()
            ->toBe($updateUserInput->getEmail())
        ->getUsername()
            ->toBe($updateUserInput->getUsername())
        ->getPassword()
            ->toBe($user->getPassword())
        ->getRole()->getCode()
            ->toBe(RoleCodeEnum::ROLE_USER->value)
        ->getCreatedAt()
            ->toBe($user->getCreatedAt())
        ->getUpdatedAt()
            ->toBeInstanceOf(DateTimeInterface::class)
            ->not()->toBe($user->getUpdatedAt()->format(\DateTimeInterface::ATOM))
    ;
});

it('can update the password of an user', function () {
    // Given
    $user = $this->makeUser();

    $updateUserInput = new UpdateUserInputDto(
        plainPassword: fake()->password(16),
    );

    $updateUserUseCase = new UpdateUserUseCase(
        $this->userFetcher,
        $this->userBuilder,
        $this->eventDispatcher,
    );

    // When
    $updateUserUseCase->updateUser($updateUserInput, $user);

    // Then
    $updatedUser = $this->userFetcher->find($user->getId());

    expect($updatedUser)
        ->toBeInstanceOf(User::class)
        ->getId()
            ->toBe($user->getId())
        ->getEmail()
            ->toBe($user->getEmail())
        ->getUsername()
            ->toBe($user->getUsername())
        ->getPassword()
            ->toBe($user->getPassword())
        ->getPlainPassword()
            ->toBeNull()
        ->getRole()->getCode()
            ->toBe(RoleCodeEnum::ROLE_USER->value)
        ->getCreatedAt()
            ->toBe($user->getCreatedAt())
        ->getUpdatedAt()
            ->toBeInstanceOf(DateTimeInterface::class)
            ->not()->toBe($user->getUpdatedAt()->format(\DateTimeInterface::ATOM))
    ;
});
