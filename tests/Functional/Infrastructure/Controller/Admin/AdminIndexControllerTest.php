<?php

declare(strict_types=1);

namespace App\Tests\Functional\Presentation\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

it('cannot access admin page when not logged in', function () {
    // When
    static::$client->request(Request::METHOD_GET, '/admin');

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_FORBIDDEN);
});

it('can access admin page when logged in as an admin', function () {
    // Given
    $user = $this->makeDefaultUser();
    $this->loginUser($user->getEmail());

    // When
    static::$client->request(Request::METHOD_GET, '/admin');

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);

    $content = $response->getContent();
    expect($content)->toContain($user->getId());
});

it('provides social networks to the template', function () {
    // Given
    $user = $this->makeDefaultUser();
    $this->loginUser($user->getEmail());

    // When
    static::$client->request(Request::METHOD_GET, '/admin');

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);

    $content = $response->getContent();

    expect($content)
        ->toContain('GITHUB')
        ->and($content)->toContain('LINKEDIN')
        ->and($content)->toContain('X')
        ->and($content)->toContain($user->getId());
});
