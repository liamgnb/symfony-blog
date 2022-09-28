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

    /**
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            "categories" => $this->categorieRepository->findBy([], ["titre" => "ASC"]),
        ]);
    }

    #[Route('/categorie/{slug}', name: 'app_categorie_slug')]
    public function articleParCategorie($slug): Response
    {
        return $this->render('categorie/articles_par_categorie.html.twig', [
            'categorie' =>  $this->categorieRepository->findOneBy(['slug' => $slug]),
        ]);
    }

}
