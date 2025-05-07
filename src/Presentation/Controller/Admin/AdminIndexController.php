<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Application\Command\User\UpdateUserProfilePictureCommand;
use App\Domain\Model\User\User;
use App\Domain\Repository\User\UserRepositoryInterface;
use App\Presentation\Components\Common\FlashBag;
use App\Presentation\Form\Admin\ProfilePictureDropZoneType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/admin', name: 'app_admin_index', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class AdminIndexController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(
        Request $request,
        MessageBusInterface $messageBus,
    ): Response {
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

        $profileImageForm = $this->createForm(ProfilePictureDropZoneType::class);

        $profileImageForm->handleRequest($request);

        if ($profileImageForm->isSubmitted() && $profileImageForm->isValid()) {
            $file = $profileImageForm->get('profilePicture')->getData();

            if (null === $file) {
                $this->addFlash(FlashBag::TYPE_ERROR, 'No file uploaded');

                return $this->redirectToRoute('app_admin_index');
            }

            /**
             * @var UploadedFile $file
             */
            $newFilename = md5(uniqid()).'.'.$file->getClientOriginalExtension();

            $fileSystem = new Filesystem();

            /** @var string $projectDir */
            $projectDir = $this->getParameter('kernel.project_dir');
            /** @var string $profilePictureProjectPath */
            $profilePictureProjectPath = $this->getParameter('app.profile_picture_path');

            $profilePictureProjectPath = $projectDir.'/public/'.$profilePictureProjectPath;

            if (!file_exists($profilePictureProjectPath)) {
                $fileSystem->mkdir($profilePictureProjectPath);
            }

            $filePath = $profilePictureProjectPath.'/'.$newFilename;
            $fileSystem->rename($file->getPathname(), $filePath);

            $messageBus->dispatch(new UpdateUserProfilePictureCommand(
                userId: $securityUserId,
                newFilename: $newFilename,
                profilePicturePath: $filePath,
            ));

            $this->addFlash(FlashBag::TYPE_SUCCESS, 'Profile picture updated');

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render(
            '@admin/index.html.twig',
            [
                'currentUserId' => $currentUser->getId(),
                'socialNetworks' => $currentUser->getSocialNetworks()->toArray(),
                'skills' => $currentUser->getSkills()->toArray(),
                'profileImageForm' => $profileImageForm,
            ]
        );
    }
}
