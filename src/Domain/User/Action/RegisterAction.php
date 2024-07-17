<?php

declare(strict_types=1);

namespace App\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;
use App\Application\Common\Exception\BadRequestHttpException;
use App\Application\Common\Exception\ValidationException;
use App\Application\Common\Exception\ValidationHttpException;
use App\Domain\User\Dto\RegisterPendingRegistrationInput;
use App\Domain\User\UseCase\RegisterPendingRegistrationUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
readonly class RegisterAction
{
    public function __construct(
        private RegisterPendingRegistrationUseCase $registerPendingRegistrationUseCase,
    ) {
    }

    #[Route('/register', name: 'register', methods: [HttpMethodEnum::POST->value])]
    public function register(
        #[MapRequestPayload] RegisterPendingRegistrationInput $registerPendingRegistrationInput,
    ): Response {
        try {
            $this->registerPendingRegistrationUseCase->registerPendingRegistration($registerPendingRegistrationInput);
        } catch (ValidationException $e) {
            throw new ValidationHttpException($e->getMessage());
        } catch (\Exception $e) {
            dump($e->getMessage());
            throw new BadRequestHttpException('User registration failed.');
        }

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
