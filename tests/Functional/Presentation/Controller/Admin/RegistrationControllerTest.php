<?php

declare(strict_types=1);

namespace App\Tests\Functional\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\Doctrine\User\Repository\DoctrineUserRepository;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->userRepository = static::getContainer()->get(DoctrineUserRepository::class);
});

it('redirects to login when users already exist', function () {
    // Given
    $this->makeDefaultUser();

    // When
    static::$client->request(Request::METHOD_GET, '/admin/register');

    // Then
    $response = static::$client->getResponse();
    expect($response->isRedirect())->toBeTrue();
    expect($response->headers->get('Location'))->toContain('/admin/login');
});

it('displays registration form when no users exist', function () {
    // When
    deleteDefaultUsers($this->userRepository);

    static::$client->request(Request::METHOD_GET, '/admin/register');

    // Then
    $response = static::$client->getResponse();
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);

    $content = $response->getContent();
    expect($content)
        ->toContain('email')
        ->and($content)->toContain('username')
        ->and($content)->toContain('password')
        ->and($content)->toContain('passwordConfirm');
});

it('creates a user and redirects to login when valid data is submitted', function () {
    // Given
    deleteDefaultUsers($this->userRepository);

    $faker = Factory::create();
    $email = $faker->email();
    $username = $faker->userName();
    $password = $faker->regexify('[A-Za-z0-9]{12}').$faker->regexify('[@#$%^&!]{2}');

    // When
    static::$client->request(
        Request::METHOD_POST,
        '/admin/register',
        [
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'passwordConfirm' => $password,
        ]
    );

    // Then
    $response = static::$client->getResponse();
    expect($response->isRedirect())->toBeTrue();
    expect($response->headers->get('Location'))->toContain('/admin/login');

    // Verify user was created in database
    $userRepository = static::getContainer()->get('App\Domain\Repository\User\UserRepositoryInterface');
    $user = $userRepository->findOneByEmail($email);

    expect($user)->not->toBeNull();
    expect($user->getUsername())->toBe($username);
});

it('shows validation errors when invalid data is submitted', function () {
    // Given
    deleteDefaultUsers($this->userRepository);

    $faker = Factory::create();

    // When
    static::$client->request(
        Request::METHOD_POST,
        '/admin/register',
        [
            'email' => $faker->word(),
            'username' => '',
            'password' => $faker->regexify('[A-Za-z0-9]{5}'),
            'passwordConfirm' => $faker->regexify('[A-Za-z0-9]{8}'),
        ]
    );

    // Then
    $response = static::$client->getResponse();
    expect($response->isRedirect())->toBeTrue();

    // Follow redirect to see flash messages
    static::$client->followRedirect();
    $content = static::$client->getResponse()->getContent();

    expect($content)->toContain('error');
});

it('redirects to login when trying to access registration with existing users', function () {
    // Given
    $faker = Factory::create();
    $this->makeDefaultUser();

    // When
    static::$client->request(
        Request::METHOD_POST,
        '/admin/register',
        [
            'email' => $faker->email(),
            'username' => $faker->userName(),
            'password' => $faker->regexify('[A-Za-z0-9]{12}').$faker->regexify('[@#$%^&!]{2}'),
            'passwordConfirm' => $faker->regexify('[A-Za-z0-9]{12}').$faker->regexify('[@#$%^&!]{2}'),
        ]
    );

    // Then
    $response = static::$client->getResponse();
    expect($response->isRedirect())->toBeTrue();
    expect($response->headers->get('Location'))->toContain('/admin/login');
});

it('tests password mismatch validation error', function () {
    // Given
    deleteDefaultUsers($this->userRepository);

    $faker = Factory::create();
    $email = $faker->email();
    $username = $faker->userName();
    $password = $faker->regexify('[A-Za-z0-9]{12}').$faker->regexify('[@#$%^&!]{2}');
    $differentPassword = $faker->regexify('[A-Za-z0-9]{12}').$faker->regexify('[@#$%^&!]{3}');

    // When
    static::$client->request(
        Request::METHOD_POST,
        '/admin/register',
        [
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'passwordConfirm' => $differentPassword,
        ]
    );

    // Then
    $response = static::$client->getResponse();
    expect($response->isRedirect())->toBeTrue();

    // Follow redirect to see flash messages
    static::$client->followRedirect();
    $content = static::$client->getResponse()->getContent();

    expect($content)->toContain('error');
});

it('tests password minimum length validation', function () {
    // Given
    deleteDefaultUsers($this->userRepository);

    $faker = Factory::create();
    $email = $faker->email();
    $username = $faker->userName();
    $shortPassword = $faker->regexify('[A-Za-z0-9]{11}');

    // When
    static::$client->request(
        Request::METHOD_POST,
        '/admin/register',
        [
            'email' => $email,
            'username' => $username,
            'password' => $shortPassword,
            'passwordConfirm' => $shortPassword,
        ]
    );

    // Then
    $response = static::$client->getResponse();
    expect($response->isRedirect())->toBeTrue();

    // Follow redirect to see flash messages
    static::$client->followRedirect();
    $content = static::$client->getResponse()->getContent();

    expect($content)->toContain('error');
});

function deleteDefaultUsers(DoctrineUserRepository $userRepository): void
{
    $existingUsers = $userRepository->findAll();

    foreach ($existingUsers as $user) {
        $userRepository->delete($user);
    }
}
