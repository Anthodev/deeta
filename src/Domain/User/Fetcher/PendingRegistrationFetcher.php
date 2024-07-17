<?php

declare(strict_types=1);

namespace App\Domain\User\Fetcher;

use App\Application\Common\Fetcher\AbstractFetcher;
use App\Domain\User\Entity\PendingRegistration;
use App\Infrastructure\Persistence\Doctrine\User\Repository\PendingRegistrationRepository;

class PendingRegistrationFetcher extends AbstractFetcher implements PendingRegistrationFetcherInterface
{
    public function __construct(
        private readonly PendingRegistrationRepository $pendingRegistrationRepository,
    ) {
        parent::__construct($pendingRegistrationRepository);
    }

    public function findOneByEmailOrUsername(string $email, string $username): ?PendingRegistration
    {
        return $this->pendingRegistrationRepository->findOneByEmailOrUsername($email, $username);
    }

    public function findOneByTokenOrFail(string $token): PendingRegistration
    {
        return $this->pendingRegistrationRepository->findOneByTokenOrFail($token);
    }
}
