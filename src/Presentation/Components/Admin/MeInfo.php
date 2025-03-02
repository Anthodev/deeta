<?php

declare(strict_types=1);

namespace App\Presentation\Components\Admin;

use App\Application\Command\User\UpdateUserCommand;
use App\Domain\Model\User\User;
use App\Presentation\Components\Common\FlashBag;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'Components/Admin/me_info.html.twig')]
final class MeInfo
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    #[LiveProp(writable: true)]
    public ?string $firstName = null;

    #[LiveProp(writable: true)]
    public ?string $lastName = null;

    #[LiveProp(writable: true)]
    public ?string $jobTitle = null;

    #[LiveProp(writable: true)]
    public ?string $company = null;

    #[LiveProp(writable: true)]
    public ?string $location = null;

    #[LiveProp(writable: true)]
    public User $user;

    public function mount(
        User $user,
    ): void {
        $this->user = $user;

        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->jobTitle = $user->getJobTitle();
        $this->company = $user->getCompany();
        $this->location = $user->getLocation();
    }

    /**
     * @throws ExceptionInterface
     */
    #[LiveAction]
    public function save(): void
    {
        if (null === $this->user->getId()) {
            throw new \RuntimeException('the user should have an id');
        }

        $this->messageBus->dispatch(new UpdateUserCommand(
            $this->user->getId(),
            $this->firstName,
            $this->lastName,
            $this->jobTitle,
            $this->company,
            $this->location,
        ));

        $this->emit(
            FlashBag::MESSAGE_NEW,
            [
                'message' => 'User updated',
                'type' => FlashBag::TYPE_SUCCESS,
            ]);
    }
}
