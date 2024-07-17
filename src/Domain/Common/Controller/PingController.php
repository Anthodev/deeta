<?php

declare(strict_types=1);

namespace App\Domain\Common\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PingController extends AbstractController
{
    #[Route('/ping', name: 'ping', methods: ['GET'])]
    public function ping(): Response
    {
        return new JsonResponse('pong', Response::HTTP_OK);
    }

    #[Route('/auth_ping', name: 'auth_ping', methods: ['GET'])]
    public function authPing(): Response
    {
        return new JsonResponse('pong', Response::HTTP_OK);
    }
}
