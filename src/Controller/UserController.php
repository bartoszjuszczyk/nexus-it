<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Service\User\AvatarUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        AvatarUploader $avatarUploader,
        EntityManagerInterface $entityManager,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                try {
                    $avatar = $avatarUploader->upload($avatarFile);
                    $user->setAvatar($avatar);
                } catch (\Exception $e) {
                    $form->addError(new FormError('There was an error due to uploading avatar image.'));

                    return $this->redirectToRoute('app_user_edit');
                }
            }
            try {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Your changes have been saved.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'There was an error saving your changes.');
            }

            return $this->redirectToRoute('app_user_edit');
        }

        return $this->render('user/edit.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – User Edit',
            'board_title' => 'User edit',
            'form' => $form,
        ]);
    }
}
