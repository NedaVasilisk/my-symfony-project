<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class PageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function redirectToHome(): Response
    {
        return $this->render("home/index.html.twig");
    }

    #[Route('/{page}', name: 'page', requirements: ['page' => 'home|about|contact|auth'])]
    public function index(string $page): Response
    {
        return $this->render("{$page}/index.html.twig");
    }
}
