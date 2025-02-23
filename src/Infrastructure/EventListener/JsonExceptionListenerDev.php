<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

#[When('dev')]
class JsonExceptionListenerDev implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 200],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $exceptionCode = $exception->getCode();

        if ($exception instanceof HttpExceptionInterface) {
            $exceptionCode = $exception->getStatusCode();
        }

        if ($exceptionCode < 400) {
            $exceptionCode = 500;
        }

        $content = [
            'code' => $exceptionCode,
            'message' => $exception->getMessage(),
        ];

        $event->setResponse(
            new JsonResponse($content, $content['code'])
        );
    }
}
