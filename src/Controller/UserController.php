<?php

namespace App\Controller;

use App\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);

        return $this->render('user/edit.html.twig', [
            'app_title' => $this->getParameter('app_title'),
            'page_title' => $this->getParameter('app_title').' – User Edit',
            'board_title' => 'User edit',
            'form' => $form,
        ]);
    }
}
