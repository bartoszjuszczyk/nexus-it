<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use App\Service\User\AvatarUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user_list')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Users',
            'boardTitle' => 'Users',
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_user_edit')]
    public function edit(
        User $user,
        Request $request,
        AvatarUploader $avatarUploader,
        EntityManagerInterface $entityManager,
    ): Response {
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
                $this->addFlash('success', 'User have been edited successfully.');

                return $this->redirectToRoute('app_user_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'There was an error editing user.');

                return $this->redirectToRoute('app_user_edit');
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form,
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – User Edit',
            'boardTitle' => 'User edit: #'.$user->getId(),
        ]);
    }

    #[Route('/user/edit', name: 'app_user_edit_mine', methods: ['GET', 'POST'])]
    public function editMine(
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

                    return $this->redirectToRoute('app_user_edit_mine');
                }
            }
            try {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Your changes have been saved.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'There was an error saving your changes.');
            }

            return $this->redirectToRoute('app_user_edit_mine');
        }

        return $this->render('user/edit.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – User Edit',
            'boardTitle' => 'User edit',
            'form' => $form,
        ]);
    }

    #[Route('/user/new', name: 'app_user_new')]
    public function create(
        Request $request,
        AvatarUploader $avatarUploader,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = new User();
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                try {
                    $avatar = $avatarUploader->upload($avatarFile);
                    $user->setAvatar($avatar);
                } catch (\Exception $e) {
                    $form->addError(new FormError('There was an error due to uploading avatar image.'));

                    return $this->redirectToRoute('app_user_new');
                }
            }
            try {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Your changes have been saved.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'There was an error saving your changes.');
            }

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/new.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Create User',
            'boardTitle' => 'Create user',
            'form' => $form,
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_user_delete')]
    public function delete(
        User $user,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User has been deleted.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'There was an error deleting user.');
        }

        return $this->redirectToRoute('app_user_list');
    }
}
