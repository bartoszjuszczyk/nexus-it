<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CmsController extends AbstractController
{
    #[Route('/', name: 'app_cms_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard.html.twig', [
            'app_title' => $this->getParameter('app_title'),
            'page_title' => $this->getParameter('app_title').' – Dashboard',
            'board_title' => 'Dashboard',
        ]);
    }
}
