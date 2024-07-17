<?php

declare(strict_types=1);

namespace App\Domain\User\Action;

use App\Application\Common\Action\BaseAction;
use App\Application\Common\Enum\HttpMethodEnum;
use App\Application\Common\Exception\BadRequestHttpException;
use App\Application\Common\Exception\BuildException;
use App\Application\Common\Exception\EntityNotFoundHttpException;
use App\Application\Common\Exception\ValidationException;
use App\Domain\User\Dto\UpdateUserInputDto;
use App\Domain\User\Fetcher\UserFetcherInterface;
use App\Domain\User\Security\UserVoter;
use App\Domain\User\UseCase\UpdateUserUseCase;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class UpdateUserAction extends BaseAction
{
    public function __construct(
        private readonly UserFetcherInterface $userFetcher,
        private readonly UpdateUserUseCase $updateUserUseCase,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/users/{id}', methods: [HttpMethodEnum::PATCH->value])]
    public function updateUser(
        string $id,
        #[MapRequestPayload] UpdateUserInputDto $userInputDto,
    ): Response {
        $user = $this->userFetcher->getOneByIdEnabled($id);

        if (null === $user) {
            throw new EntityNotFoundHttpException($id);
        }

        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        try {
            $this->updateUserUseCase->updateUser($userInputDto, $user);
        } catch (BuildException|ValidationException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return $this->output($user);
    }
}
