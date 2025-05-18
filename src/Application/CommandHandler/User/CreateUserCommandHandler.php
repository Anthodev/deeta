<?php

declare(strict_types=1);

namespace App\Application\CommandHandler\User;

use App\Application\Command\User\CreateUserCommand;
use App\Application\Query\User\GetRoleQuery;
use App\Domain\Model\User\Role;
use App\Domain\Model\User\User;
use App\Domain\Repository\User\UserRepositoryInterface;
use App\Infrastructure\Enum\RoleCodeEnum;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsMessageHandler]
class CreateUserCommandHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(CreateUserCommand $command): void
    {
        $envelope = $this->messageBus->dispatch(new GetRoleQuery(RoleCodeEnum::ROLE_ADMIN->value));
        $roleAdmin = $this->getContentFromEnvelope($envelope);

        $user = new User(
            email: $command->getEmail(),
            username: $command->getUsername(),
            plainPassword: $command->getPassword(),
            enabled: 1,
            role: $roleAdmin,
        );

        $this->userRepository->save($user);
    }

    private function getContentFromEnvelope(Envelope $envelope): ?Role
    {
        $stamp = $envelope->last(HandledStamp::class);

        if (!$stamp) {
            $this->logger->error('Role retrieval failed');

            return null;
        }

        /** @var Role|null $result */
        $result = $stamp->getResult();

        if (null === $result) {
            return null;
        }

        return $result;
    }
}
