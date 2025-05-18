<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\QueryHandler\User;

use App\Application\Query\User\GetRoleQuery;
use App\Application\QueryHandler\User\GetRoleQueryHandler;
use App\Domain\Model\User\Role;
use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineRoleRepository;
use Faker\Factory;
use PHPUnit\Framework\MockObject\MockObject;

beforeEach(function () {
    $this->roleRepository = $this->createMock(DoctrineRoleRepository::class);
    $this->handler = new GetRoleQueryHandler(
        $this->roleRepository,
    );
    $this->faker = Factory::create();
});

it('returns a role when one is found', function () {
    // Given
    $roleCode = $this->faker->word();

    /** @var MockObject&Role */
    $role = $this->createMock(Role::class);
    $role->method('getCode')->willReturn($roleCode);
    $role->method('getLabel')->willReturn($this->faker->sentence());

    $query = new GetRoleQuery($roleCode);

    $this->roleRepository
        ->expects($this->once())
        ->method('findOneBy')
        ->with(['code' => $roleCode])
        ->willReturn($role);

    // When
    $result = $this->handler->__invoke($query);

    // Then
    expect($result)
        ->toBeInstanceOf(Role::class)
        ->and($result->getCode())->toBe($roleCode);
});

it('returns null when no role is found', function () {
    // Given
    $roleCode = $this->faker->word();
    $query = new GetRoleQuery($roleCode);

    $this->roleRepository
        ->expects($this->once())
        ->method('findOneBy')
        ->with(['code' => $roleCode])
        ->willReturn(null);

    // When
    $result = $this->handler->__invoke($query);

    // Then
    expect($result)->toBeNull();
});
