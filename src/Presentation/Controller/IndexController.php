<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Domain\Model\User\User;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/', name: 'app_home', methods: [Request::METHOD_GET])]
class IndexController extends AbstractController
{
    public function __invoke(
        UserRepositoryInterface $userRepository,
    ): Response {
        /** @var User|null $user */
        $user = $userRepository->findAll()[0] ?? null;
        $userSortedSkills = $user?->getSkills()->toArray();
        $userSortedSocialNetworks = $user?->getSocialNetworks()->toArray();

        if (null === $userSortedSkills) {
            $userSortedSkills = [];
        } else {
            usort($userSortedSkills, function ($a, $b) {
                return $a->getPosition() <=> $b->getPosition();
            });
        }

        if (null === $userSortedSocialNetworks) {
            $userSortedSocialNetworks = [];
        } else {
            usort($userSortedSocialNetworks, function ($a, $b) {
                return $a->getPosition() <=> $b->getPosition();
            });
        }

        return $this->render('@app/index.html.twig', [
            'user' => $user,
            'userSortedSkills' => $userSortedSkills,
            'userSortedSocialNetworks' => $userSortedSocialNetworks,
        ]);
    }
}
