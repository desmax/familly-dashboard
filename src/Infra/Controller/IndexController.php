<?php

declare(strict_types=1);

namespace App\Infra\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function __invoke(): Response
    {
        return $this->render('index.html.twig');
    }
}
