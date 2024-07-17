<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;
use Symfony\Component\HttpFoundation\Response;

it('can get the users list', function () {
    // Given
    $user = $this->makeUser();

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    // When
    static::$client->request(HttpMethodEnum::GET->value, '/api/users');

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    expect($response->getContent())->toBeJson();

    $data = json_decode($response->getContent(), true);
    $firstUser = $data[0];

    expect($firstUser['id'])
        ->toBeString()
        ->toBe($user->getId()->toRfc4122())
        ->and($firstUser['createdAt'])
            ->toBeString()
            ->toBe($user->getCreatedAt()->format(\DateTimeInterface::ATOM))
        ->and($firstUser['updatedAt'])
            ->toBeString()
            ->toBe($user->getUpdatedAt()->format(\DateTimeInterface::ATOM))
        ->and($firstUser['role']['id'])
            ->toBeString()
            ->toBe($user->getRole()->getId()->toRfc4122())
    ;
});

it('cannot get the users list if not authenticated', function () {
    // When
    static::$client->request(HttpMethodEnum::GET->value, '/api/users');

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":401,"message":"JWT Token not found"}');
});
