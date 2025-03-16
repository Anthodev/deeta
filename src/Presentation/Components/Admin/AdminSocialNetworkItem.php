<?php

declare(strict_types=1);

namespace App\Presentation\Components\Admin;

use App\Application\Command\Info\UpdateSocialNetworksCommand;
use App\Presentation\Components\Common\FlashBag;
use App\Shared\Enum\SocialNetworkEnum;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'Components/Admin/admin_social_network_item.html.twig')]
final class AdminSocialNetworkItem
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    /**
     * @var array<string, string>
     */
    #[LiveProp(writable: false, useSerializerForHydration: true)]
    public array $socialNetworksOptions = [];

    #[LiveProp(writable: true, useSerializerForHydration: true)]
    public string $label = '';

    #[LiveProp(writable: true, useSerializerForHydration: true)]
    public string $url = '';

    #[LiveProp(writable: true, useSerializerForHydration: true)]
    public string $network = '';

    #[LiveProp(writable: true, useSerializerForHydration: true)]
    public string $position = '0';

    #[LiveProp(useSerializerForHydration: true)]
    public string $socialNetworkId = '';

    #[LiveProp(useSerializerForHydration: true)]
    public string $userId = '';

    #[LiveProp(useSerializerForHydration: true)]
    public string $componentId = '';

    #[LiveProp(useSerializerForHydration: true)]
    public bool $initialized = false;

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
    ) {
        $this->socialNetworksOptions = SocialNetworkEnum::toLabelArray();
    }

    public function mount(
        string $label,
        string $url,
        string $network,
        int $position,
        string $socialNetworkId,
        string $userId,
        string $componentId = '',
    ): void {
        // Only set the initial values if not already initialized
        if (!$this->initialized) {
            $this->label = $label;
            $this->url = $url;
            $this->network = $network;
            $this->position = (string) $position;
            $this->socialNetworkId = $socialNetworkId;
            $this->userId = $userId;
            $this->componentId = $componentId;
            $this->initialized = true;
        }
    }

    #[LiveAction]
    public function update(): void
    {
        try {
            $this->messageBus->dispatch(new UpdateSocialNetworksCommand(
                socialNetworkId: $this->socialNetworkId,
                userId: $this->userId,
                label: $this->label,
                network: $this->network,
                url: $this->url,
                position: (int) $this->position,
            ));

            $this->emit(
                FlashBag::MESSAGE_NEW,
                [
                    'message' => 'Social network updated',
                    'type' => FlashBag::TYPE_SUCCESS,
                ]
            );

            $this->refreshSocialNetworksOptions();
        } catch (\Exception $exception) {
            $this->logger->error('Error updating social network: '.$exception->getMessage());

            $this->emit(
                FlashBag::MESSAGE_NEW,
                [
                    'message' => $exception->getMessage(),
                    'type' => FlashBag::TYPE_ERROR,
                ]
            );

            $this->refreshSocialNetworksOptions();
        }
    }

    #[LiveAction]
    public function refreshSocialNetworksOptions(): void
    {
        $this->socialNetworksOptions = SocialNetworkEnum::toLabelArray();
    }
}
