<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category_list')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Categories',
            'boardTitle' => 'Categories',
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/category/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(
        Category $category,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($category);
                $entityManager->flush();
                $this->addFlash('success', 'Category edited successfully.');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error editing the category.');
            }

            return $this->redirectToRoute('app_category_list');
        }

        return $this->render('category/edit.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Edit Category',
            'boardTitle' => 'Edit category',
            'form' => $form,
        ]);
    }

    #[Route('/category/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($category);
                $entityManager->flush();
                $this->addFlash('success', 'Category created successfully.');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error adding the category.');
            }

            return $this->redirectToRoute('app_category_list');
        }

        return $this->render('category/new.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – New Category',
            'boardTitle' => 'New category',
            'form' => $form,
        ]);
    }

    #[Route('/category/{id}/delete', name: 'app_category_delete', methods: ['POST'])]
    public function delete(
        Category $category,
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response {
        try {
            if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
                $entityManager->remove($category);
                $entityManager->flush();

                $this->addFlash('success', 'Category deleted successfully.');
            }
        } catch (\Exception $exception) {
            $this->addFlash('error', 'There was an error deleting the category.');
        }

        return $this->redirectToRoute('app_category_list');
    }
}
