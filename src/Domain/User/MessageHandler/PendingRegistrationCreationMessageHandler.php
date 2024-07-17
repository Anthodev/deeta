<?php

declare(strict_types=1);

namespace App\Domain\User\MessageHandler;

use App\Domain\User\Entity\PendingRegistration;
use App\Domain\User\Fetcher\PendingRegistrationFetcherInterface;
use App\Domain\User\Message\PendingRegistrationCreationMessage;
use App\Domain\User\Service\UserMailer;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsMessageHandler]
readonly class PendingRegistrationCreationMessageHandler
{
    public function __construct(
        private PendingRegistrationFetcherInterface $pendingRegistrationFetcher,
        private UserMailer $userMailer,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(PendingRegistrationCreationMessage $pendingRegistrationCreationMessage): void
    {
        $pendingRegistration = $this->pendingRegistrationFetcher->find($pendingRegistrationCreationMessage->getPendingRegistrationId());

        if (null === $pendingRegistration || !$pendingRegistration instanceof PendingRegistration) {
            throw new \InvalidArgumentException();
        }

        $this->userMailer->sendConfirmationEmail($pendingRegistration);
    }
}
