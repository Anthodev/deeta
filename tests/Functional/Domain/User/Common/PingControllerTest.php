<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\User\Common;

use App\Application\Common\Enum\HttpMethodEnum;
use Symfony\Component\HttpFoundation\Response;

it('can ping the api', function () {
    // When
    static::$client->request(HttpMethodEnum::GET->value, '/api/ping');

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    expect($response->getContent())->toBe('"pong"');
});

it('cannot ping the authenticated api', function () {
    // When
    static::$client->request(HttpMethodEnum::GET->value, '/api/auth_ping');

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);

    $data = json_decode($response->getContent(), true);
    expect($data['code'])
        ->toBe(Response::HTTP_UNAUTHORIZED)
        ->and($data['message'])
            ->toBe('JWT Token not found')
    ;
});

it('can ping the authenticated api', function () {
    // Given
    $user = $this->makeDefaultUser();
    $this->loginUser($user->getEmail());

    // When
    static::$client->request(HttpMethodEnum::GET->value, '/api/auth_ping');

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    expect($response->getContent())->toBe('"pong"');
});

it('cannot ping the authenticated api with disabled user', function () {
    // Given
    $user = $this->makeUser(
        enabled: false,
    );

    $data = [
        'username' => $user->getEmail(),
        'password' => 'test1234',
    ];
    $dataEncoded = json_encode($data, JSON_THROW_ON_ERROR);

    // When
    static::$client->request(
        method: HttpMethodEnum::POST->value,
        uri: '/api/login_check',
        content: $dataEncoded,
    );

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);

    $data = json_decode($response->getContent(), true);
    expect($data['code'])
        ->toBe(Response::HTTP_UNAUTHORIZED)
        ->and($data['message'])
            ->toBe('User is not enabled.')
    ;
});
