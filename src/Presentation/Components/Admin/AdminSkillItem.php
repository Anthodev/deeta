<?php

declare(strict_types=1);

namespace App\Presentation\Components\Admin;

use App\Application\Command\Info\DeleteSkillCommand;
use App\Presentation\Components\Common\FlashBag;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'Components/Admin/admin_skill_item.html.twig')]
final class AdminSkillItem
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp(writable: true, useSerializerForHydration: true)]
    public string $label = '';

    #[LiveProp(writable: true, useSerializerForHydration: true)]
    public bool $isTextWhite = false;

    #[LiveProp(writable: true, useSerializerForHydration: true)]
    public ?string $defaultColor = null;

    #[LiveProp(writable: true, useSerializerForHydration: true)]
    public string $position = '0';

    #[LiveProp(useSerializerForHydration: true)]
    public string $skillId = '';

    #[LiveProp(useSerializerForHydration: true)]
    public string $userId = '';

    #[LiveProp(useSerializerForHydration: true)]
    public string $componentId = '';

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function mount(
        string $label,
        bool $isTextWhite,
        ?string $defaultColor,
        int $position,
        string $skillId,
        string $userId,
        string $componentId = '',
    ): void {
        $this->label = $label;
        $this->isTextWhite = $isTextWhite;
        $this->defaultColor = $defaultColor;
        $this->position = (string) $position;
        $this->skillId = $skillId;
        $this->userId = $userId;
        $this->componentId = $componentId;
    }

    #[LiveAction]
    public function delete(): void
    {
        try {
            $this->messageBus->dispatch(new DeleteSkillCommand(
                skillId: $this->skillId,
                userId: $this->userId,
            ));

            $this->emit(
                FlashBag::MESSAGE_NEW,
                [
                    'message' => 'Compétence supprimée',
                    'type' => FlashBag::TYPE_SUCCESS,
                ]
            );

            $this->emit('skill-deleted');
        } catch (\Exception $exception) {
            $this->logger->error('Erreur lors de la suppression de la compétence: '.$exception->getMessage());

            $this->emit(
                FlashBag::MESSAGE_NEW,
                [
                    'message' => $exception->getMessage(),
                    'type' => FlashBag::TYPE_ERROR,
                ]
            );
        }
    }
}
