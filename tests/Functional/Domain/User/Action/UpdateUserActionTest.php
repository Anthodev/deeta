<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Faker\fake;

it('can update an user', function () {
    // Given
    $user = $this->makeDefaultUser();

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    $newEmail = fake()->email();
    $newUsername = fake()->userName();

    $dataEncoded = json_encode([
        'email' => $newEmail,
        'username' => $newUsername,
    ], JSON_THROW_ON_ERROR);

    // When
    static::$client->request(
        method: HttpMethodEnum::PATCH->value,
        uri: '/api/users/' . $user->getId()->toRfc4122(),
        content: $dataEncoded,
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    expect($response->getContent())->toBeJson();

    $userRepository = $this->getContainer()->get(UserRepository::class);
    $updatedUser = $userRepository->find($user->getId());

    $data = json_decode($response->getContent(), true);

    expect($data['id'])
        ->toBeString()
        ->toBe($user->getId()->toRfc4122())
        ->and($data['email'])
            ->toBeString()
            ->toBe($newEmail)
        ->and($data['username'])
            ->toBeString()
            ->toBe($newUsername)
        ->and($updatedUser->getPlainPassword())
            ->toBeNull()
        ->and($data['role']['id'])
            ->toBeString()
            ->toBe($user->getRole()->getId()->toRfc4122())
    ;
});

it('can update the password of an user', function () {
    // Given
    $user = $this->makeDefaultUser();

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    $newPassword = fake()->password(16);

    $dataEncoded = json_encode([
        'plainPassword' => $newPassword,
    ], JSON_THROW_ON_ERROR);

    // When
    static::$client->request(
        method: HttpMethodEnum::PATCH->value,
        uri: '/api/users/' . $user->getId()->toRfc4122(),
        content: $dataEncoded,
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    expect($response->getContent())->toBeJson();

    $data = json_decode($response->getContent(), true);

    expect($data['id'])
        ->toBeString()
        ->toBe($user->getId()->toRfc4122())
        ->and($data['email'])
            ->toBeString()
            ->toBe($user->getEmail())
        ->and($data['role']['id'])
            ->toBeString()
            ->toBe($user->getRole()->getId()->toRfc4122())
    ;

    $data = [
        'username' => $user->getEmail(),
        'password' => $newPassword,
    ];
    $dataEncoded = json_encode($data, JSON_THROW_ON_ERROR);

    static::$client->setServerParameter('HTTP_authorization', '');

    // When
    static::$client->request(
        method: HttpMethodEnum::POST->value,
        uri: '/api/login_check',
        content: $dataEncoded,
    );

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
});

it('can update the username and the password of an user', function () {
    // Given
    $user = $this->makeDefaultUser();

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    $newUsername = fake()->userName();
    $newPassword = fake()->password(16);

    $dataEncoded = json_encode([
        'username' => $newUsername,
        'plainPassword' => $newPassword,
    ], JSON_THROW_ON_ERROR);

    // When
    static::$client->request(
        method: HttpMethodEnum::PATCH->value,
        uri: '/api/users/' . $user->getId()->toRfc4122(),
        content: $dataEncoded,
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    expect($response->getContent())->toBeJson();

    $data = json_decode($response->getContent(), true);

    expect($data['id'])
        ->toBeString()
        ->toBe($user->getId()->toRfc4122())
        ->and($data['email'])
            ->toBeString()
            ->toBe($user->getEmail())
        ->and($data['role']['id'])
            ->toBeString()
            ->toBe($user->getRole()->getId()->toRfc4122())
    ;

    $data = [
        'username' => $user->getEmail(),
        'password' => $newPassword,
    ];
    $dataEncoded = json_encode($data, JSON_THROW_ON_ERROR);

    static::$client->setServerParameter('HTTP_authorization', '');

    // When
    static::$client->request(
        method: HttpMethodEnum::POST->value,
        uri: '/api/login_check',
        content: $dataEncoded,
    );

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
});

it('cannot update a user that does not exist', function () {
    // Given
    $user = $this->makeDefaultUser();
    $this->loginUser(self::DEFAULT_USER_EMAIL);

    $fakeUuid = fake()->uuid();

    $dataEncoded = json_encode([
        'email' => fake()->email(),
        'username' => fake()->userName(),
    ], JSON_THROW_ON_ERROR);

    // When
    static::$client->request(
        method: HttpMethodEnum::PATCH->value,
        uri: '/api/users/' . $fakeUuid,
        content: $dataEncoded,
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":404,"message":"Data not found with id ' . $fakeUuid . '"}');
});

it('cannot update a disabled user', function () {
    // Given
    $this->makeDefaultUser();

    $user = $this->makeUser(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(),
        enabled: false,
    );

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    $dataEncoded = json_encode([
        'email' => fake()->email(),
        'username' => fake()->userName(),
    ], JSON_THROW_ON_ERROR);

    // When
    static::$client->request(
        method: HttpMethodEnum::PATCH->value,
        uri: '/api/users/' . $user->getId()->toRfc4122(),
        content: $dataEncoded,
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":404,"message":"Data not found with id ' . $user->getId()->toRfc4122() . '"}');
});

it('cannot update another user with a role user', function () {
    // Given
    $this->makeUser();

    $user = $this->makeUser(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(),
    );

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    $dataEncoded = json_encode([
        'email' => fake()->email(),
        'username' => fake()->userName(),
    ], JSON_THROW_ON_ERROR);

    // When
    static::$client->request(
        method: HttpMethodEnum::PATCH->value,
        uri: '/api/users/' . $user->getId()->toRfc4122(),
        content: $dataEncoded,
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_FORBIDDEN);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":403,"message":"You do not have sufficient permissions to access this resource."}');
});
