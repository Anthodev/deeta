<?php

declare(strict_types=1);

namespace App\Domain\User\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PendingRegistrationNotExists extends Constraint
{
    public const string EMAIL_EXISTS_ERROR_CODE = 'e12c8f25-7c24-4b9b-b99c-47c787dba4d0';
    public const string USERNAME_EXISTS_ERROR_CODE = '20fa1700-6ce2-4cb6-baa0-2c93cd225665';
    public string $messageEmail = 'This email is already used.';
    public string $messageUsername = 'This username is already used.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
