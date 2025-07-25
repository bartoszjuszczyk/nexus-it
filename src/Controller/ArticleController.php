<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Type\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article_list', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Articles',
            'boardTitle' => 'Articles',
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/article/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($article);
                $entityManager->flush();
                $this->addFlash('success', 'Article edited successfully.');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error editing the article.');
            }

            return $this->redirectToRoute('app_article_list');
        }

        return $this->render('article/edit.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Edit Article',
            'boardTitle' => 'Edit article',
            'form' => $form,
        ]);
    }

    #[Route('/article/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($article);
                $entityManager->flush();
                $this->addFlash('success', 'Article created successfully.');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error adding the article.');
            }

            return $this->redirectToRoute('app_article_list');
        }

        return $this->render('article/new.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – New Article',
            'boardTitle' => 'New article',
            'form' => $form,
        ]);
    }

    #[Route('/article/{id}/delete', name: 'app_article_delete', methods: ['POST'])]
    public function delete(
        Article $article,
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response {
        try {
            if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
                $entityManager->remove($article);
                $entityManager->flush();

                $this->addFlash('success', 'Article deleted successfully.');
            }
        } catch (\Exception $exception) {
            $this->addFlash('error', 'There was an error deleting the article.');
        }

        return $this->redirectToRoute('app_article_list');
    }
}
