<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/', name: 'app_home', methods: [Request::METHOD_GET])]
class IndexController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('@app/index.html.twig');
    }
}
