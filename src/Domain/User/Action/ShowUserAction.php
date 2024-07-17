<?php

declare(strict_types=1);

namespace App\Domain\User\Action;

use App\Application\Common\Action\BaseAction;
use App\Application\Common\Enum\HttpMethodEnum;
use App\Application\Common\Exception\EntityNotFoundHttpException;
use App\Domain\User\Fetcher\UserFetcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class ShowUserAction extends BaseAction
{
    public function __construct(
        private readonly UserFetcherInterface $userFetcher,
    ) {
    }

    #[Route('/users/{id}', methods: [HttpMethodEnum::GET->value])]
    public function getUser(string $id): Response
    {
        $user = $this->userFetcher->getOneByIdEnabled($id);

        if (null === $user) {
            throw new EntityNotFoundHttpException($id);
        }

        return $this->output($user);
    }
}
