<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Application\Command\User\CreateUserCommand;
use App\Domain\Repository\User\UserRepositoryInterface;
use App\Presentation\Request\CreateUserWebRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/admin/register', name: 'app_admin_register', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(
        Request $request,
        MessageBusInterface $messageBus,
        ValidatorInterface $validator,
    ): Response {
        $users = $this->userRepository->findAll();
        $hasUsers = count($users) > 0;

        $error = '';

        if (false === $hasUsers) {
            if ($request->isMethod(Request::METHOD_POST)) {
                /** @var string $email */
                $email = $request->request->get('email') ?? '';
                /** @var string $username */
                $username = $request->request->get('username') ?? '';
                /** @var string $password */
                $password = $request->request->get('password') ?? '';
                /** @var string $passwordConfirm */
                $passwordConfirm = $request->request->get('passwordConfirm') ?? '';

                $createUserWebRequest = new CreateUserWebRequest(
                    email: $email,
                    username: $username,
                    password: $password,
                    passwordConfirm: $passwordConfirm,
                );

                $errors = $validator->validate($createUserWebRequest);

                if ($errors->count() > 0) {
                    foreach ($errors as $error) {
                        $this->addFlash('error', $error->getMessage());
                    }

                    return $this->redirectToRoute('app_admin_register');
                }

                try {
                    $messageBus->dispatch(
                        new CreateUserCommand(
                            email: $email,
                            username: $username,
                            password: $password,
                            passwordConfirm: $passwordConfirm,
                        )
                    );
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                    $this->addFlash('error', 'Unable to create user. Please try again later.');
                }

                return $this->redirectToRoute('app_admin_login');
            }

            return $this->render('@admin/register.html.twig');
        }

        return $this->redirectToRoute('app_admin_login');
    }
}
