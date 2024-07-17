<?php

namespace App\Tests\Functional\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;
use App\Domain\User\Factory\PendingRegistrationFactory;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Fetcher\PendingRegistrationFetcherInterface;
use App\Infrastructure\Persistence\Doctrine\User\Repository\PendingRegistrationRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\RoleRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Faker\fake;

beforeEach(function () {
    $this->pendingRegistrationFetcher = $this->getContainer()->get(PendingRegistrationFetcherInterface::class);
    $this->userRepository = $this->getContainer()->get(UserRepository::class);
    $this->roleRepository = $this->getContainer()->get(RoleRepository::class);
});

it('can confirm an email', function () {
    // Given
    $email = fake()->email();
    $token = md5($email);

    $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
        email: $email,
        username: fake()->userName(),
        password: fake()->password(16),
    );
    $pendingRegistration->setRole($this->roleRepository->findOneBy(['code' => 'ROLE_USER']));
    $pendingRegistration->setToken($token);
    $this->entityManager->persist($pendingRegistration);
    $this->entityManager->flush();

    // When
    static::$client->request(
        HttpMethodEnum::GET->value,
        '/api/register/confirm/' . $token,
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_NO_CONTENT);

    $user = $this->userRepository->findOneBy(['email' => $email]);

    expect($user)
        ->not()->toBeNull()
        ->isEnabled()->toBeTrue()
    ;

    $pendingRegistration = $this->pendingRegistrationFetcher->findOneBy(['email' => $email]);
    expect($pendingRegistration)->toBeNull();
});

it('cannot confirm an email with an invalid token', function () {
    // Given
    $token = fake()->word();

    // When
    static::$client->request(
        HttpMethodEnum::GET->value,
        "/api/register/confirm/$token",
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toBe('The confirmation token has expired, is invalid or you must register first.');
});

it('cannot confirm with an existing user', function () {
    // Given
    $email = fake()->email();
    $token = md5($email);

    $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
        email: $email,
        username: fake()->userName(),
        password: fake()->password(16),
    );
    $pendingRegistration->setRole($this->roleRepository->findOneBy(['code' => 'ROLE_USER']));
    $pendingRegistration->setToken($token);

    $user = UserFactory::makeUser(
        email: $email,
        username: fake()->userName(),
        password: fake()->password(16),
    );
    $this->userRepository->save($user);

    // When
    static::$client->request(
        HttpMethodEnum::GET->value,
        '/api/register/confirm/' . $token,
    );

    // Then
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toBe('The confirmation token has expired, is invalid or you must register first.');
});
