<?php

declare(strict_types=1);

namespace App\Shared\Dto\Info;

use Symfony\Component\Serializer\Attribute\Groups;

#[Groups(['social_network'])]
readonly class SocialNetworkDto
{
    public function __construct(
        public string $networkId,
        public string $networkIcon,
        public string $networkLabel,
        public string $networkUrl,
        public int $networkPosition,
        public string $userId,
        public string $networkDefaultColor = '',
        public string $__dto_type = 'social_network',
    ) {
    }
}
