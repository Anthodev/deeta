<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\User\Validator;

use App\Domain\User\Factory\PendingRegistrationFactory;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Validator\PendingRegistrationNotExists;
use App\Domain\User\Validator\PendingRegistrationNotExistsValidator;
use App\Infrastructure\Persistence\Doctrine\User\Repository\PendingRegistrationRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PendingRegistrationNotExistsValidatorTest extends ConstraintValidatorTestCase
{
    private Generator $faker;
    private PendingRegistrationRepository|MockObject $pendingRegistrationRepository;
    private UserRepository|MockObject $userFetcher;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
        $this->pendingRegistrationRepository = $this->createMock(PendingRegistrationRepository::class);
        $this->userFetcher = $this->createMock(UserRepository::class);

        parent::setUp();
    }

    #[\Override] protected function createValidator(): ConstraintValidatorInterface
    {
        return new PendingRegistrationNotExistsValidator(
            $this->pendingRegistrationRepository,
            $this->userFetcher,
        );
    }

    public function testPendingRegistrationWithUsernameExists(): void
    {
        // Given
        $emailPendingFaked = $this->faker->email();
        $emailUserFaked = $this->faker->email();
        $username = $this->faker->userName();
        $password = $this->faker->password(16);

        $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
            $emailPendingFaked,
            $username,
            $password,
        );

        $this->pendingRegistrationRepository
            ->expects(self::once())
            ->method('findOneByEmailOrUsername')
            ->with($emailUserFaked, $pendingRegistration->getUsername())
            ->willReturn($pendingRegistration)
        ;

        $this->userFetcher
            ->expects(self::once())
            ->method('findOneByEmailOrUsername')
            ->with($emailUserFaked, $pendingRegistration->getUsername())
            ->willReturn(null)
        ;

        $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
            $emailUserFaked,
            $username,
            $password,
        );

        // When
        $this->validator->validate($pendingRegistration, new PendingRegistrationNotExists());

        // Then
        $this
            ->buildViolation('This username is already used.')
            ->setCode(PendingRegistrationNotExists::USERNAME_EXISTS_ERROR_CODE)
            ->assertRaised()
        ;
    }

    public function testPendingRegistrationWithEmailExists(): void
    {
        // Given
        $email = $this->faker->email();
        $usernamePendingFaked = $this->faker->userName();
        $usernameUserFaked = $this->faker->userName();
        $password = $this->faker->password(16);

        $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
            $email,
            $usernamePendingFaked,
            $password,
        );

        $this->pendingRegistrationRepository
            ->expects(self::once())
            ->method('findOneByEmailOrUsername')
            ->with($pendingRegistration->getEmail(), $usernameUserFaked)
            ->willReturn($pendingRegistration)
        ;

        $this->userFetcher
            ->expects(self::once())
            ->method('findOneByEmailOrUsername')
            ->with($pendingRegistration->getEmail(), $usernameUserFaked)
            ->willReturn(null)
        ;

        $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
            $email,
            $usernameUserFaked,
            $password,
        );

        // When
        $this->validator->validate($pendingRegistration, new PendingRegistrationNotExists());

        // Then
        $this
            ->buildViolation('This email is already used.')
            ->setCode(PendingRegistrationNotExists::EMAIL_EXISTS_ERROR_CODE)
            ->assertRaised()
        ;
    }

    public function testPendingRegistrationWithEmailExistsOnUser(): void
    {
        // Given
        $email = $this->faker->email();
        $usernamePendingFaked = $this->faker->userName();
        $usernameUserFaked = $this->faker->userName();
        $password = $this->faker->password(16);

        $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
            $email,
            $usernamePendingFaked,
            $password,
        );

        $user = UserFactory::makeUser(
            email: $email,
            username: $usernameUserFaked,
            password: $password,
        );

        $this->pendingRegistrationRepository
            ->expects(self::once())
            ->method('findOneByEmailOrUsername')
            ->with($pendingRegistration->getEmail(), $usernamePendingFaked)
            ->willReturn(null)
        ;

        $this->userFetcher
            ->expects(self::once())
            ->method('findOneByEmailOrUsername')
            ->with($pendingRegistration->getEmail(), $usernamePendingFaked)
            ->willReturn($user)
        ;

        $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
            $email,
            $usernamePendingFaked,
            $password,
        );

        // When
        $this->validator->validate($pendingRegistration, new PendingRegistrationNotExists());

        // Then
        $this
            ->buildViolation('This email is already used.')
            ->setCode(PendingRegistrationNotExists::EMAIL_EXISTS_ERROR_CODE)
            ->assertRaised()
        ;
    }
}
