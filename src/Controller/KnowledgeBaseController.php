<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class KnowledgeBaseController extends AbstractController
{
    #[Route('/kb', name: 'app_kb_index', methods: ['GET'])]
    public function index(
        Request $request,
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
    ): Response {
        $categoryId = $request->query->get('category');

        $categoryId = $categoryId ? (int) $categoryId : null;

        $articles = $articleRepository->findForKnowledgeBase($categoryId);

        $allCategories = $categoryRepository->findBy([], ['name' => 'ASC']);

        return $this->render('kb/index.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Knowledge Base',
            'boardTitle' => 'Knowledge Base',
            'articles' => $articles,
            'categories' => $allCategories,
        ]);
    }

    #[Route('/kb/article/{id}', name: 'app_kb_article_show', methods: ['GET'])]
    public function showArticle(Article $article): Response
    {
        return $this->render('kb/article_show.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Article – '.$article->getTitle(),
            'boardTitle' => 'Article: '.$article->getTitle(),
            'article' => $article,
        ]);
    }
}
