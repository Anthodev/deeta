<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Fetcher\RoleFetcher;
use App\Domain\User\Fetcher\UserFetcher;
use App\Infrastructure\Persistence\Doctrine\User\Repository\RoleRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use function Pest\Faker\fake;

it('can register a user', function () {
    // When
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(201);
});

it('cannot register a user with an invalid password', function () {
    // When
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => fake()->userName(),
            'password' => fake()->password(1, 5),
        ]
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(422);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toBe('This value is too short. It should have 12 characters or more.');
});

it('cannot register a user with an invalid email', function () {
    // When
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => 'invalid-email',
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(422);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toBe('This value is not a valid email address.');
});

it('cannot register a user with an existing email', function () {
    // Given
    $email = fake()->email();

    // When
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => $email,
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(201);

    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => $email,
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(400);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toContain('This email is already used.');
});

it('cannot register a user with an existing username', function () {
    // Given
    $username = fake()->userName();

    // When
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => $username,
            'password' => fake()->password(16),
        ]
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(201);

    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => $username,
            'password' => fake()->password(16),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(400);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toContain('This username is already used.');
});

it('cannot register a user with an invalid payload', function () {
    // When
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => fake()->userName(),
        ]
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(422);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toContain('This value should be of type string.');
});

it('cannot register a pending registration with an existing user email', function () {
    // Given
    $userFetcher = $this->getContainer()->get(UserFetcher::class);
    $roleFetcher = $this->getContainer()->get(RoleFetcher::class);

    $email = fake()->email();
    $username = fake()->userName();
    $password = fake()->password(16);

    $roleUser = $roleFetcher->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]);

    $user = UserFactory::makeUser(
        email: $email,
        username: $username,
        password: $password,
    );
    $user->setRole($roleUser);
    $userFetcher->save($user);

    // When
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => $email,
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(400);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toContain('This email is already used.');
});
