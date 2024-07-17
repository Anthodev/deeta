<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Domain\User\Entity\PendingRegistration;
use App\Domain\User\Entity\Role;
use App\Domain\User\Entity\User;

class UserFactory
{
    public static function makeUser(
        string $email,
        string $username,
        string $password
    ): User {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPlainPassword($password);

        return $user;
    }

    public static function makeVerifiedUser(
        string $email,
        string $username,
        string $password
    ): User {
        $user = self::makeUser($email, $username, $password);
        $user->setEnabled(true);

        return $user;
    }

    public static function makeVerifiedUserWithRole(
        string $email,
        string $username,
        string $password,
        Role $role
    ): User {
        $user = self::makeVerifiedUser($email, $username, $password);
        $user->setRole($role);

        return $user;
    }

    public static function makeVerifiedUserFromPendingRegistration(
        PendingRegistration $pendingRegistration
    ): User {
        $user = new User();
        $user->setEmail($pendingRegistration->getEmail());
        $user->setUsername($pendingRegistration->getUsername());
        $user->setPassword($pendingRegistration->getPassword());
        $user->setRole($pendingRegistration->getRole());
        $user->setEnabled(true);
        $user->setPlainPassword(null);

        return $user;
    }
}
