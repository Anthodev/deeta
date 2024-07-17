<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\User\Entity\PendingRegistration;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Fetcher\PendingRegistrationFetcherInterface;
use App\Domain\User\Fetcher\UserFetcherInterface;

readonly class ConfirmEmailUseCase
{
    public function __construct(
        private UserFetcherInterface $userFetcher,
        private PendingRegistrationFetcherInterface $pendingRegistrationFetcher,
    ) {
    }

    public function registerUser(
        PendingRegistration $pendingRegistration,
    ): void {
        $user = UserFactory::makeVerifiedUserFromPendingRegistration(
            $pendingRegistration,
        );

        $this->userFetcher->save($user);
        $this->pendingRegistrationFetcher->delete($pendingRegistration);
    }
}
