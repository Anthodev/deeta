<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin', name: 'app_admin_index', methods: [Request::METHOD_GET])]
class AdminIndexController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('@admin/index.html.twig');
    }
}
