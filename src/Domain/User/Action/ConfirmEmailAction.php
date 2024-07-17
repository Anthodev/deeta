<?php

declare(strict_types=1);

namespace App\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;
use App\Application\Common\Exception\BadRequestHttpException;
use App\Application\Common\Exception\EntityNotFoundException;
use App\Application\Common\Exception\InternalServerHttpException;
use App\Domain\User\Fetcher\PendingRegistrationFetcherInterface;
use App\Domain\User\UseCase\ConfirmEmailUseCase;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
readonly class ConfirmEmailAction
{
    public function __construct(
        private PendingRegistrationFetcherInterface $pendingRegistrationFetcher,
        private ConfirmEmailUseCase $confirmEmailUseCase,
    ) {
    }

    #[Route('/register/confirm/{token}', name: 'confirm_email', methods: [HttpMethodEnum::GET->value])]
    public function confirmEmail(
        string $token,
    ): Response {
        try {
            $pendingRegistration = $this->pendingRegistrationFetcher->findOneByTokenOrFail($token);
        } catch (EntityNotFoundException) {
            throw new BadRequestHttpException('The confirmation token has expired, is invalid or you must register first.');
        } catch (NonUniqueResultException) {
            throw new InternalServerHttpException();
        }

        $this->confirmEmailUseCase->registerUser($pendingRegistration);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
