<?php

declare(strict_types=1);

namespace App\Application\Query\Info;

use App\Shared\Dto\Info\SocialNetworkDto;
use Symfony\Component\Serializer\Annotation\Groups;

final class GetSocialNetworksForUserResponse
{
    /**
     * @param SocialNetworkDto[] $content
     */
    public function __construct(
        #[Groups(['social_network'])]
        private readonly array $content,
    ) {
    }

    /**
     * @return SocialNetworkDto[]
     */
    #[Groups(['social_network'])]
    public function getContent(): array
    {
        return $this->content;
    }
}
