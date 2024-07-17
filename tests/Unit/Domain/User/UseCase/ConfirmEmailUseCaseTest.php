<?php

namespace App\Tests\Unit\Domain\User\UseCase;

use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Factory\PendingRegistrationFactory;
use App\Domain\User\Fetcher\PendingRegistrationFetcher;
use App\Domain\User\Fetcher\RoleFetcherInterface;
use App\Domain\User\Fetcher\UserFetcherInterface;
use App\Domain\User\UseCase\ConfirmEmailUseCase;
use DateTimeInterface;
use Symfony\Component\Uid\Ulid;
use function Pest\Faker\fake;

beforeEach(function () {
    $this->pendingRegistrationFetcher = $this->createMock(PendingRegistrationFetcher::class);
    $this->userFetcher = $this->getContainer()->get(UserFetcherInterface::class);
    $this->roleFetcher = $this->getContainer()->get(RoleFetcherInterface::class);
});

it('can canfirm an email', function () {
    // Given
    $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(16),
    );
    $pendingRegistration->setPassword($pendingRegistration->getPlainPassword());
    $pendingRegistration->setRole($this->roleFetcher->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]));

    $confirmEmailUseCase = new ConfirmEmailUseCase(
        $this->userFetcher,
        $this->pendingRegistrationFetcher,
    );

    // When
    $confirmEmailUseCase->registerUser($pendingRegistration);

    // Then
    $user = $this->userFetcher->findOneBy(['email' => $pendingRegistration->getEmail()]);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->getId()
            ->not()->toBeNull()
            ->toBeInstanceOf(Ulid::class)
        ->getEmail()
            ->toBe($pendingRegistration->getEmail())
        ->getUsername()
            ->toBe($pendingRegistration->getUsername())
        ->getRole()->getCode()
            ->toBe(RoleCodeEnum::ROLE_USER->value)
        ->getPassword()
            ->not()->toBeNull()
            ->toBe($pendingRegistration->getPassword())
        ->getPlainPassword()->toBeNull()
        ->getCreatedAt()
            ->not()->toBeNull()
            ->toBeInstanceOf(DateTimeInterface::class)
        ->getUpdatedAt()
            ->not()->toBeNull()
            ->toBeInstanceOf(DateTimeInterface::class)
        ->isEnabled()
            ->toBeTrue()
    ;
});
