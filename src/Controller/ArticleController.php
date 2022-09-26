<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    // Création d'un objet ArticleRepository par symfony à l'appel de la méthode index()
    // INJECTION DE DEPENDANCES
    private ArticleRepository $articleRepository;

    // Demande à symfony d'injecter une instance de ArticleRepository

    /**
     * @param ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }


    #[Route('/articles', name: 'app_articles')]
    public function index(): Response
    {
        //dd($repository->findAll());
        return $this->render('article/index.html.twig', [
            'articles' => $this->articleRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/article/{slug}', name: 'app_article_slug')]
    public function detail($slug): Response
    {
        //dd( $this->articleRepository->findBy(['slug' => $slug]));
        return $this->render('article/detail.html.twig', [
            'article' => $this->articleRepository->findOneBy(['slug' => $slug]),
        ]);
    }

}
