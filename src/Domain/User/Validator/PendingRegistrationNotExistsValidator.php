<?php

declare(strict_types=1);

namespace App\Domain\User\Validator;

use App\Domain\User\Entity\PendingRegistration;
use App\Infrastructure\Persistence\Doctrine\User\Repository\PendingRegistrationRepository;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PendingRegistrationNotExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly PendingRegistrationRepository $pendingRegistrationRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof PendingRegistration) {
            return;
        }

        if (!$constraint instanceof PendingRegistrationNotExists) {
            return;
        }

        $user = $this->userRepository->findOneByEmailOrUsername($value->getEmail(), $value->getUsername());
        $pendingRegistration = $this->pendingRegistrationRepository->findOneByEmailOrUsername($value->getEmail(), $value->getUsername());

        $isEmailError = false;
        $isUsernameError = false;

        if (null !== $user) {
            $isEmailError = $value->getEmail() === $user->getEmail();
            $isUsernameError = $value->getUsername() === $user->getUsername();
        }

        if (null !== $pendingRegistration) {
            $isEmailError = $value->getEmail() === $pendingRegistration->getEmail();
            $isUsernameError = $value->getUsername() === $pendingRegistration->getUsername();
        }

        if ($isEmailError) {
            $this->context->buildViolation($constraint->messageEmail)
                ->setCode(PendingRegistrationNotExists::EMAIL_EXISTS_ERROR_CODE)
                ->addViolation();
        }

        if ($isUsernameError) {
            $this->context->buildViolation($constraint->messageUsername)
                ->setCode(PendingRegistrationNotExists::USERNAME_EXISTS_ERROR_CODE)
                ->addViolation();
        }
    }
}
