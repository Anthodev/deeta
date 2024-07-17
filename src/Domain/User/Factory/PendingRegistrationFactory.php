<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Domain\User\Entity\PendingRegistration;

class PendingRegistrationFactory
{
    public static function makePendingRegistration(
        string $email,
        string $username,
        string $password
    ): PendingRegistration {
        $pendingRegistration = new PendingRegistration();
        $pendingRegistration->setEmail($email);
        $pendingRegistration->setUsername($username);
        $pendingRegistration->setPlainPassword($password);

        return $pendingRegistration;
    }
}
