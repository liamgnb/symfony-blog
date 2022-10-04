<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        //dd($repository->findAll());

        // Mise en place de la pagination
        $articles = $paginator->paginate(
            $this->articleRepository->findBy([], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }


    #[Route('/articles/{slug}', name: 'app_articles_slug')]
    public function detail($slug): Response
    {
        //dd( $this->articleRepository->findBy(['slug' => $slug]));
        return $this->render('article/detail.html.twig', [
            'article' => $this->articleRepository->findOneBy(['slug' => $slug]),
        ]);
    }

    #[Route('/articles/nouveau', name: 'app_articles_nouveau', methods: ['GET', 'POST'], priority: 1)]
    public function insert(SluggerInterface $slugger, Request $request) : Response
    {
        $article = new Article();
        // création du form
        $formArticle = $this->createForm(ArticleType::class, $article);

        // reconnait si le form à été soumis
        $formArticle->handleRequest($request);
        if($formArticle->isSubmitted() && $formArticle->isValid()){
            $article->setSlug($slugger->slug($article->getTitre())->lower())
                    ->setCreatedAt(new \DateTime());
            $this->articleRepository->add($article, true);
            return $this->redirectToRoute('app_articles');
        }


        // appel de la vue 'twig' permettant d'afficher le form
        return $this->renderForm('article/nouveau.html.twig', [
            'formArticle' => $formArticle,
        ]);
    }
}
