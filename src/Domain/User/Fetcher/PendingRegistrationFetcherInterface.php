<?php

declare(strict_types=1);

namespace App\Domain\User\Fetcher;

use App\Application\Common\Exception\EntityNotFoundException;
use App\Application\Common\Fetcher\AbstractFetcherInterface;
use App\Domain\User\Entity\PendingRegistration;
use Doctrine\ORM\NonUniqueResultException;

interface PendingRegistrationFetcherInterface extends AbstractFetcherInterface
{
    /**
     * @throws NonUniqueResultException
     */
    public function findOneByEmailOrUsername(string $email, string $username): ?PendingRegistration;

    /**
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     */
    public function findOneByTokenOrFail(string $token): PendingRegistration;
}
