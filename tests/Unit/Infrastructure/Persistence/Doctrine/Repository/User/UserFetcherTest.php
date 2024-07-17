<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Persistence\Doctrine\Repository\User;

use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Fetcher\UserFetcher;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use Symfony\Component\Uid\Ulid;
use function Pest\Faker\fake;

beforeEach(function () {
    $this->userRepository = $this->createMock(UserRepository::class);
    $this->userFetcher = new UserFetcher($this->userRepository);
});

it('can fetch user', function () {
    // Given
    $user = UserFactory::makeUser(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(),
    );

    $userId = new Ulid();
    $user->setId($userId);

    $this->userRepository
        ->expects($this->once())
        ->method('find')
        ->with($userId->toRfc4122())
        ->willReturn($user);

    // When
    $userDb = $this->userFetcher->find($userId->toRfc4122());

    // Then
    expect($userDb)->toBe($user);
});
