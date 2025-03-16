<?php

declare(strict_types=1);

namespace App\Application\QueryHandler\Info;

use App\Application\Query\Info\GetSocialNetworksForUserQuery;
use App\Application\Query\Info\GetSocialNetworksForUserResponse;
use App\Domain\Model\Info\SocialNetwork;
use App\Domain\Repository\Info\SocialNetworkRepositoryInterface;
use App\Shared\Dto\Info\SocialNetworkDto;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetSocialNetworksForUserQueryHandler
{
    public function __construct(
        private readonly SocialNetworkRepositoryInterface $socialNetworkRepository,
    ) {
    }

    public function __invoke(GetSocialNetworksForUserQuery $query): GetSocialNetworksForUserResponse
    {
        $socialNetworks = $this->socialNetworkRepository->findNetworksByUserId($query->getUserId());

        $socialNetworkDtos = [];

        /** @var SocialNetwork $socialNetwork */
        foreach ($socialNetworks as $socialNetwork) {
            $socialNetworkDtos[] = new SocialNetworkDto(
                id: $socialNetwork->getId() ?? '',
                network: $socialNetwork->getNetwork(),
                label: $socialNetwork->getLabel(),
                url: $socialNetwork->getUrl(),
                position: $socialNetwork->getPosition() ?? 0,
                userId: $socialNetwork->getUser()->getId() ?? '',
            );
        }

        return new GetSocialNetworksForUserResponse($socialNetworkDtos);
    }
}
