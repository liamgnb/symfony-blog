<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    // Création d'un objet ArticleRepository par symfony à l'appel de la méthode index()
    #[Route('/articles', name: 'app_articles')]
    public function index(ArticleRepository $repository): Response
    {
        //dd($repository->findAll());
        return $this->render('article/index.html.twig', [
            'articles' => $repository->findBy([], ['createdAt' => 'DESC'], 5),
        ]);
    }

    #[Route('/article/{id}', name: 'app_article_detail')]
    public function detail($id, ArticleRepository $repository): Response
    {
        //dd($repository->findAll());
        return $this->render('article/detail.html.twig', [
            'articles' => $repository->find($id),
        ]);
    }
}
