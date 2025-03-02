<?php

declare(strict_types=1);

namespace App\Presentation\Components\Common;

use Symfony\Component\Uid\Uuid;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'Components/Common/flash_bag.html.twig')]
final class FlashBag
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public const string MESSAGE_NEW = 'flash_bag.message.new';
    public const string TYPE_INFO = 'info';
    public const string TYPE_SUCCESS = 'success';
    public const string TYPE_WARNING = 'warning';
    public const string TYPE_ERROR = 'error';

    #[LiveProp]
    /** @phpstan-ignore-next-line */
    public array $messages = [];

    #[LiveListener(self::MESSAGE_NEW)]
    public function messageCreated(
        #[LiveArg] string $message,
        #[LiveArg] string $type,
    ): void {
        $id = Uuid::v4()->__toString();

        $this->messages[$id] = [
            'content' => $message,
            'type' => $type,
        ];
    }

    #[LiveAction]
    public function removeMessage(#[LiveArg] string $id): void
    {
        if (isset($this->messages[$id])) {
            unset($this->messages[$id]);
        }
    }
}
