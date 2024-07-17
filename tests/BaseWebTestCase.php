<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Common\Manager\JwtPayloadManager;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Factory\UserFactory;
use App\Infrastructure\Persistence\Doctrine\User\Repository\RoleRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use App\Tests\Trait\WebUtilsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class BaseWebTestCase extends WebTestCase
{
    use WebUtilsTrait;

    protected const string DEFAULT_USER_EMAIL = 'test@test.io';

    protected readonly EntityManagerInterface $entityManager;
    protected static KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        static::$client = static::createClient(
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
        );

        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    public function createAuthenticatedClient(string $email): void
    {
        static::$client = static::createClient();
        $this->loginUser($email);
    }

    public function loginUser(string $email): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::$client->getContainer()->get(UserRepository::class);

        /** @var User|UserInterface $user */
        $user = $userRepository->findOneBy(['email' => $email]);

        static::$client->loginUser($user);

        /** @var JwtPayloadManager $jwtPayloadManager */
        $jwtPayloadManager = static::$client->getContainer()->get(JwtPayloadManager::class);
        $token = $jwtPayloadManager->getJwtToken();

        static::$client->setServerParameter('HTTP_authorization', 'Bearer ' . $token);
    }

    public function getUser(): User
    {
        $security = $this->getSecurity();
        $user = $security->getUser();

        static::assertInstanceOf(User::class, $user);

        return $user;
    }

    public function makeDefaultUser(): User
    {
        $roleRepository = static::getContainer()->get(RoleRepository::class);
        $roleAdmin = $roleRepository->findOneBy(['code' => RoleCodeEnum::ROLE_ADMIN->value]);

        $user = UserFactory::makeVerifiedUserWithRole(
            email: self::DEFAULT_USER_EMAIL,
            username: 'Test',
            password: 'test1234',
            role: $roleAdmin
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function makeUser(
        string $email = self::DEFAULT_USER_EMAIL,
        string $username = 'Test',
        string $password = 'test1234',
        bool $enabled = true,
    ): User {
        $roleRepository = static::getContainer()->get(RoleRepository::class);
        $roleUser = $roleRepository->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]);

        $user = UserFactory::makeVerifiedUserWithRole(
            email: $email,
            username: $username,
            password: $password,
            role: $roleUser
        );

        if (false === $enabled) {
            $user->setEnabled(false);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
