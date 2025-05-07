<?php

declare(strict_types=1);

namespace App\Presentation\Components\Admin;

use App\Application\Command\User\DeleteUserProfilePictureCommand;
use App\Presentation\Components\Common\FlashBag;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'Components/Admin/admin_profile_picture.html.twig')]
class AdminProfilePicture
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp(writable: true)]
    public string $userId;

    #[LiveProp(writable: true)]
    public string $username;

    #[LiveProp(writable: true, useSerializerForHydration: true)]
    public FormView $profileImageForm;

    #[LiveProp(writable: true)]
    public ?string $profilePicturePath = null;

    #[LiveProp(writable: true)]
    public int $profileDeletedComponentId = 0;

    #[LiveProp(writable: true)]
    public bool $reloadPage = false;

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function mount(
        string $userId,
        string $username,
        FormView $profileImageForm,
        ?string $profilePicturePath = null,
    ): void {
        $this->userId = $userId;
        $this->username = $username;
        $this->profileImageForm = $profileImageForm;
        $this->profilePicturePath = $profilePicturePath;
        $this->profileDeletedComponentId = rand(1, 100);
    }

    #[LiveAction]
    public function delete(): void
    {
        try {
            $this->messageBus->dispatch(
                new DeleteUserProfilePictureCommand(userId: $this->userId)
            );

            $this->profilePicturePath = null;
            $this->reloadPage = true;

            $this->emit(FlashBag::MESSAGE_NEW, [
                'message' => 'Image supprimée',
                'type' => FlashBag::TYPE_SUCCESS,
            ]);
        } catch (ExceptionInterface $e) {
            $this->logger->error(
                'Erreur lors de la suppression de l\'image de profil'
            );
            $this->emit(FlashBag::MESSAGE_NEW, [
                'message' => 'Erreur lors de la suppression de l\'image',
                'type' => FlashBag::TYPE_ERROR,
            ]);
        }
    }
}
