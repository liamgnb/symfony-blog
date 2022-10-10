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
     */
    public function __construct(CategorieRepository $categorieRepository, ArticleRepository $articleRepository)
    {
        $this->categorieRepository = $categorieRepository;
        $this->articleRepository = $articleRepository;
    }


    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {

        $categoriesDB = $this->categorieRepository->findBy([], ["titre" => "ASC"]);
        $categories = [];
        foreach ($categoriesDB as $categorie){
            $nbr = 0;
            foreach ($categorie->getArticles() as $article){
                if($article->isEstPublie()){
                    $nbr++;
                }
            }
            $categories[] = ['categorie' => $categorie, 'nbrArticle' => $nbr];
        }
        return $this->render('categorie/index.html.twig', [
            "categories" => $categories,
        ]);
    }

    #[Route('/categories/{slug}', name: 'app_categories_slug')]
    public function articleParCategorie($slug): Response
    {

        $categorie = $this->categorieRepository->findOneBy(['slug' => $slug]);
        $articles = $this->articleRepository->findBy(['categorie' => $categorie->getId(), 'estPublie' => 'true']);
        return $this->render('categorie/articles_par_categorie.html.twig', [
            'categorie' => $categorie,
            'articles' => $articles,
        ]);
    }

}
