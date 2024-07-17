<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Persistence\Doctrine\Repository\User;

use App\Domain\User\Factory\PendingRegistrationFactory;
use App\Domain\User\Fetcher\PendingRegistrationFetcher;
use App\Infrastructure\Persistence\Doctrine\User\Repository\PendingRegistrationRepository;
use Symfony\Component\Uid\Ulid;
use function Pest\Faker\fake;

beforeEach(function () {
    $this->pendingRegistrationRepository = $this->createMock(PendingRegistrationRepository::class);
    $this->pendingRegistrationFetcher = new PendingRegistrationFetcher($this->pendingRegistrationRepository);
});

it('can fetch a pending registration', function () {
    // Given
    $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(),
    );

    $pendingRegistrationId = new Ulid();
    $pendingRegistration->setId($pendingRegistrationId);

    $this->pendingRegistrationRepository
        ->expects($this->once())
        ->method('find')
        ->with($pendingRegistrationId->toRfc4122())
        ->willReturn($pendingRegistration);

    // When
    $pendingRegistrationDb = $this->pendingRegistrationFetcher->find($pendingRegistrationId->toRfc4122());

    // Then
    expect($pendingRegistrationDb)->toBe($pendingRegistration);
});
