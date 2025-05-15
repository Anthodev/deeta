<?php

declare(strict_types=1);

namespace App\Presentation\Components\Admin;

use App\Application\Command\Info\CreateSocialNetworksCommand;
use App\Application\Query\Info\GetSocialNetworksForUserQuery;
use App\Domain\Model\Info\SocialNetwork;
use App\Presentation\Components\Common\FlashBag;
use App\Shared\Dto\Info\SocialNetworkDto;
use App\Shared\Enum\SocialNetworkEnum;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'Components/Admin/me_social_networks.html.twig')]
final class MeSocialNetworks
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public ?string $userId = null;

    /**
     * @var array<int, SocialNetworkDto>
     */
    #[LiveProp(
        writable: false,
        useSerializerForHydration: true,
        serializationContext: [
            'groups' => ['social_network'],
            'item_type' => SocialNetworkDto::class,
        ]
    )]
    public array $socialNetworks = [];

    /**
     * @var array<string, string>
     */
    #[LiveProp]
    public array $socialNetworksOptions;

    #[LiveProp(writable: true)]
    public string $socialNetworkIcon = '';

    #[LiveProp(writable: true)]
    public string $socialNetworkName = '';

    #[LiveProp(writable: true)]
    public string $socialNetworkUrl = '';

    #[LiveProp(writable: true)]
    public int $socialNetworkPosition = 0;

    /**
     * @var array<string, string>
     */
    #[LiveProp]
    public array $errors = [];

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
    ) {
        $this->socialNetworksOptions = SocialNetworkEnum::toLabelArray();
        $this->errors = [];
    }

    /**
     * @param SocialNetwork[] $socialNetworks
     */
    public function mount(
        string $userId,
        array $socialNetworks,
    ): void {
        $this->userId = $userId;
        $this->socialNetworks = array_map(
            fn (SocialNetwork $socialNetwork): SocialNetworkDto => new SocialNetworkDto(
                networkId: $socialNetwork->getId() ?? '',
                networkIcon: $socialNetwork->getNetwork(),
                networkLabel: $socialNetwork->getLabel(),
                networkUrl: $socialNetwork->getUrl(),
                networkPosition: $socialNetwork->getPosition() ?? 0,
                userId: $socialNetwork->getUser()->getId() ?? '',
            ),
            $socialNetworks
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[LiveAction]
    public function save(): void
    {
        // Validate form fields
        $this->errors = [];
        $isValid = true;

        if (empty($this->socialNetworkIcon)) {
            $this->errors['socialNetworkIcon'] = 'Please select a network';
            $isValid = false;
        }

        if (empty($this->socialNetworkName)) {
            $this->errors['socialNetworkName'] = 'Please enter a label';
            $isValid = false;
        }

        if (empty($this->socialNetworkUrl)) {
            $this->errors['socialNetworkUrl'] = 'Please enter a URL';
            $isValid = false;
        }

        if (!$isValid) {
            return;
        }

        $userId = $this->userId;

        if (null === $userId) {
            $this->logger->error('Cannot create social network, user id is null');

            $this->emit(
                FlashBag::MESSAGE_NEW,
                [
                    'message' => 'Please reconnect to your account',
                    'type' => FlashBag::TYPE_ERROR,
                ]);

            $this->resetForm();

            return;
        }

        $this->messageBus->dispatch(new CreateSocialNetworksCommand(
            userId: $userId,
            label: $this->socialNetworkName,
            network: $this->socialNetworkIcon,
            url: $this->socialNetworkUrl,
            position: $this->socialNetworkPosition,
        ));

        $this->emit(
            FlashBag::MESSAGE_NEW,
            [
                'message' => 'Social network created',
                'type' => FlashBag::TYPE_SUCCESS,
            ]);

        $this->resetForm();

        $envelope = $this->messageBus->dispatch(new GetSocialNetworksForUserQuery(
            userId: $userId,
        ));

        $this->socialNetworks = $this->getContentFromMessage($envelope);
    }

    private function resetForm(): void
    {
        $this->socialNetworkName = '';
        $this->socialNetworkUrl = '';
        $this->socialNetworkIcon = '';
        $this->socialNetworkPosition = 0;
        $this->errors = [];
    }

    /**
     * @return array<int, SocialNetworkDto>
     */
    private function getContentFromMessage(Envelope $envelope): array
    {
        $stamp = $envelope->last(HandledStamp::class);
        if (!$stamp) {
            $this->logger->error('Get social networks for user failed');

            return [];
        }

        $result = $stamp->getResult();

        if (null === $result) {
            return [];
        }

        try {
            if (!is_object($result) || !method_exists($result, 'getContent')) {
                $this->logger->error('Result does not have getContent method');

                return [];
            }

            $content = $result->getContent();

            if (is_array($content) && (!empty($content) && $content[0] instanceof SocialNetworkDto)) {
                return $content;
            }

            if (!is_array($content)) {
                $this->logger->error('Social networks content is not an array');

                return [];
            }

            $validSocialNetworks = [];

            foreach ($content as $item) {
                if ($item instanceof SocialNetworkDto) {
                    $validSocialNetworks[] = $item;
                } else {
                    $itemType = is_object($item) ? get_class($item) : gettype($item);
                    $this->logger->error('Type d\'objet incorrect dans les compétences: '.$itemType);
                }
            }

            /** @var array<int, SocialNetworkDto> */
            return $validSocialNetworks;
        } catch (\Throwable $e) {
            $this->logger->error('Error getting social networks: '.$e->getMessage());

            return [];
        }
    }
}
