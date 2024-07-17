<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Persistence\Doctrine\Repository\User;

use App\Domain\User\Entity\Role;
use App\Domain\User\Fetcher\RoleFetcher;
use App\Infrastructure\Persistence\Doctrine\User\Repository\RoleRepository;
use Symfony\Component\Uid\Ulid;
use function Pest\Faker\fake;

beforeEach(function () {
    $this->roleRepository = $this->createMock(RoleRepository::class);
    $this->roleFetcher = new RoleFetcher($this->roleRepository);
});

it('it can fetch a role', function() {
    // Given
    $role = new Role();
    $role->setCode(fake()->word());
    $role->setLabel(fake()->word());

    $roleId = new Ulid();
    $role->setId($roleId);

    $this->roleRepository
        ->expects($this->once())
        ->method('find')
        ->with($roleId->toRfc4122())
        ->willReturn($role);

    // When
    $roleDb = $this->roleFetcher->find($roleId->toRfc4122());

    // Then
    expect($roleDb)->toBe($role);
});
