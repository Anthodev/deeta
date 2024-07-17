<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Faker\fake;

it('can get an user', function () {
    // Given
    $user = $this->makeUser();

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    // When
    static::$client->request(HttpMethodEnum::GET->value, '/api/users/' . $user->getId()->toRfc4122());

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    expect($response->getContent())->toBeJson();

    $data = json_decode($response->getContent(), true);

    expect($data['id'])
        ->toBeString()
        ->toBe($user->getId()->toRfc4122())
        ->and($data['createdAt'])
            ->toBeString()
            ->toBe($user->getCreatedAt()->format(\DateTimeInterface::ATOM))
        ->and($data['updatedAt'])
            ->toBeString()
            ->toBe($user->getUpdatedAt()->format(\DateTimeInterface::ATOM))
        ->and($data['role']['id'])
            ->toBeString()
            ->toBe($user->getRole()->getId()->toRfc4122())
    ;
});

it('cannot get an user if not authenticated', function () {
    // Given
    $fakeUuid = fake()->uuid();

    // When
    static::$client->request(HttpMethodEnum::GET->value, '/api/users/' . $fakeUuid);

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":401,"message":"JWT Token not found"}');
});

it('cannot get an user that does not exist', function () {
    // Given
    $this->makeUser();
    $this->loginUser(self::DEFAULT_USER_EMAIL);
    $fakeUuid = fake()->uuid();

    // When
    static::$client->request(HttpMethodEnum::GET->value, '/api/users/' . $fakeUuid);

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":404,"message":"Data not found with id ' . $fakeUuid . '"}');
});

it('cannot get an user that is disabled', function () {
    // Given
    $this->makeUser();

    $user = $this->makeUser(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(),
        enabled: false,
    );

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    // When
    static::$client->request(HttpMethodEnum::GET->value, '/api/users/' . $user->getId()->toRfc4122());

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":404,"message":"Data not found with id ' . $user->getId()->toRfc4122() . '"}');
});
