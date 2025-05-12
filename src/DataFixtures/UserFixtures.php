<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Model\User\Role;
use App\Domain\Model\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const string ADMIN_USER_REFERENCE = 'admin-user';

    public function load(ObjectManager $manager): void
    {
        $user = new User(
            email: 'admin@test.io',
            username: 'admin',
            firstName: 'Admin',
            lastName: 'Test',
            plainPassword: 'test',
            enabled: 1,
        );

        /** @var Role $adminRole */
        $adminRole = $this->getReference(RoleFixtures::ROLE_ADMIN_REFERENCE, Role::class);

        $user->setRole($adminRole);
        $manager->persist($user);

        $this->addReference(self::ADMIN_USER_REFERENCE, $user);

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            RoleFixtures::class,
        ];
    }
}
