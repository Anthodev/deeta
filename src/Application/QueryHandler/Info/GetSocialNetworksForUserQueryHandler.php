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
                networkId: $socialNetwork->getId() ?? '',
                networkIcon: $socialNetwork->getNetwork(),
                networkLabel: $socialNetwork->getLabel(),
                networkUrl: $socialNetwork->getUrl(),
                networkPosition: $socialNetwork->getPosition() ?? 0,
                userId: $socialNetwork->getUser()->getId() ?? '',
            );
        }

        return new GetSocialNetworksForUserResponse($socialNetworkDtos);
    }
}
