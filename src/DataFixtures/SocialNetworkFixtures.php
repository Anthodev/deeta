<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Model\Info\SocialNetwork;
use App\Domain\Model\User\User;
use App\Shared\Enum\SocialNetworkEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SocialNetworkFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class);

        $github = new SocialNetwork(
            label: 'GitHub',
            url: 'https://github.com/admin',
            network: SocialNetworkEnum::GITHUB->name,
            user: $adminUser,
            defaultColor: '#24292e',
            position: 1,
        );
        $github->setDefaultId();
        $manager->persist($github);

        $linkedin = new SocialNetwork(
            label: 'LinkedIn',
            url: 'https://linkedin.com/in/admin',
            network: SocialNetworkEnum::LINKEDIN->name,
            user: $adminUser,
            defaultColor: '#0077b5',
            position: 2,
        );
        $linkedin->setDefaultId();
        $manager->persist($linkedin);

        $twitter = new SocialNetwork(
            label: 'X',
            url: 'https://twitter.com/admin',
            network: SocialNetworkEnum::X->name,
            user: $adminUser,
            defaultColor: '#1da1f2',
            position: 3,
        );
        $twitter->setDefaultId();
        $manager->persist($twitter);

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
