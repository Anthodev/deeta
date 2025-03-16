<?php

declare(strict_types=1);

namespace App\Application\Command\Info;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'sync')]
readonly class UpdateSocialNetworksCommand
{
    public function __construct(
        private string $socialNetworkId,
        private string $userId,
        private string $label,
        private string $network,
        private string $url,
        private int $position,
    ) {
    }

    public function getSocialNetworkId(): string
    {
        return $this->socialNetworkId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
