<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Domain\Model\User\User;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/admin', name: 'app_admin_index', methods: [Request::METHOD_GET])]
class AdminIndexController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(): Response
    {
        /** @var UserInterface|User|null $securityUser */
        $securityUser = $this->security->getUser();

        if (null === $securityUser) {
            throw new \RuntimeException('User not found');
        }

        /** @var User $securityUser */
        $securityUserId = $securityUser->getId();

        if (null === $securityUserId) {
            throw new \RuntimeException('User not found');
        }

        $currentUser = $this->userRepository->find($securityUserId);

        if (null === $currentUser) {
            throw new \RuntimeException('User not found in repository');
        }

        return $this->render(
            '@admin/index.html.twig',
            [
                'currentUserId' => $currentUser->getId(),
                'socialNetworks' => $currentUser->getSocialNetworks()->toArray(),
            ]
        );
    }
}
