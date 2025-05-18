<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Domain\Model\User\User;
use App\Domain\Repository\User\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/admin/login', name: 'app_admin_login', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class LoginController extends AbstractController
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly Security $security,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(
        Request $request,
        MessageBusInterface $messageBus,
    ): Response {
        /**
         * @var User[] $users
         */
        $users = $this->userRepository->findAll();
        $hasNoUsers = 0 === count($users);

        if ($hasNoUsers) {
            return $this->redirectToRoute('app_admin_register');
        }

        if (null !== $this->security->getUser()) {
            return $this->redirectToRoute('app_admin_index');
        }

        $error = $this->authenticationUtils->getLastAuthenticationError();

        if ($error) {
            $this->addFlash('error', $error->getMessage());
        }

        return $this->render('@admin/login.html.twig', [
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'error' => $error,
        ]);
    }
}
