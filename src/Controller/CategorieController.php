<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    private CategorieRepository $categorieRepository;
    private ArticleRepository $articleRepository;

    /**
     * @param CategorieRepository $categorieRepository
     * @param ArticleRepository $articleRepository
     */public function __construct(CategorieRepository $categorieRepository, ArticleRepository $articleRepository)
    {
        $this->categorieRepository = $categorieRepository;
        $this->articleRepository = $articleRepository;
    }


    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        $categoriesDB =  $this->categorieRepository->findBy([], ["titre" => "ASC"]);
        $categories = [];
        foreach ($categoriesDB as $key => $categorieDB) {
            $categories[$key]['categorie'] = $categorieDB;
            $categories[$key]['nbr_article'] = $this->articleRepository->count(["categorie" => $categorieDB->getId()]);
        }
        //dd($categoriesDB, $categories);

        return $this->render('categorie/index.html.twig', [
            "categories" => $categories,
        ]);
    }

}
