<?php

declare(strict_types=1);

namespace App\Shared\Dto\Info;

use Symfony\Component\Serializer\Annotation\Groups;

#[Groups(['social_network'])]
readonly class SocialNetworkDto
{
    public function __construct(
        #[Groups(['social_network'])]
        public string $id,

        #[Groups(['social_network'])]
        public string $network,

        #[Groups(['social_network'])]
        public string $label,

        #[Groups(['social_network'])]
        public string $url,

        #[Groups(['social_network'])]
        public int $position,

        #[Groups(['social_network'])]
        public string $userId,
    ) {
    }
}
