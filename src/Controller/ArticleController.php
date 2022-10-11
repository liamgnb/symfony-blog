<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Auteur;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\AuteurRepository;
use App\Repository\CommentaireRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    // Création d'un objet ArticleRepository par symfony à l'appel de la méthode index()
    // INJECTION DE DEPENDANCES
    private ArticleRepository $articleRepository;
    private CommentaireRepository $commentaireRepository;
    private AuteurRepository $auteurRepository;

    // Demande à symfony d'injecter une instance de ArticleRepository

    /**
     * @param ArticleRepository $articleRepository
     * @param CommentaireRepository $commentaireRepository
     * @param AuteurRepository $auteurRepository
     */
    public function __construct(ArticleRepository $articleRepository, CommentaireRepository $commentaireRepository, AuteurRepository $auteurRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->commentaireRepository = $commentaireRepository;
        $this->auteurRepository = $auteurRepository;
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


    #[Route('/articles/{slug}', name: 'app_articles_slug', methods: ['GET', 'POST'])]
    public function detail($slug, Request $request): Response
    {
        $commentaire = new Commentaire();
        $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);

        $formCommentaire->handleRequest($request);
        if($formCommentaire->isSubmitted() && $formCommentaire->isValid()){
            // vérrification du nom si pseudo saisie
            if(!$formCommentaire->get('auteur')->get('pseudo')->isEmpty() && $formCommentaire->get('auteur')->get('nom')->isEmpty()){
                $formCommentaire->get('auteur')->get('nom')->addError(new FormError('Veuillez saisir le nom'));
                return $this->renderForm('article/detail.html.twig', [
                    'article' => $this->articleRepository->findOneBy(['slug' => $slug]),
                    'formCommentaire' => $formCommentaire,
                ]);
            }
            // vérification du prénim si pseudo saisie
            if(!$formCommentaire->get('auteur')->get('pseudo')->isEmpty() && $formCommentaire->get('auteur')->get('prenom')->isEmpty()){
                $formCommentaire->get('auteur')->get('prenom')->addError(new FormError('Veuillez saisir le prénom'));
                return $this->renderForm('article/detail.html.twig', [
                    'article' => $this->articleRepository->findOneBy(['slug' => $slug]),
                    'formCommentaire' => $formCommentaire,
                ]);
            }

            // Ajout de l'auteur si il n'est pas présent dans la table.
            if($commentaire->getAuteur()->getPseudo() && !$this->auteurRepository->findOneBy(['pseudo' => $commentaire->getAuteur()->getPseudo()])){
                $this->auteurRepository->add($commentaire->getAuteur(), true);
            } elseif (!$commentaire->getAuteur()->getPseudo()){
                $commentaire->setAuteur(null);
            } else {
                $commentaire->setAuteur($this->auteurRepository->findOneBy(['pseudo' => $commentaire->getAuteur()->getPseudo()]));
            }
            $commentaire->setCreatedAt(new \DateTime());
            $this->commentaireRepository->add($commentaire, true);
            return $this->redirectToRoute('app_articles_slug', ['slug' => $slug]);
        }

        // si article n'est pas publié quitter la page
        $article = $this->articleRepository->findOneBy(['slug' => $slug]);
        if (!$article->isEstPublie()) {
            return $this->redirectToRoute('app_articles');
        }

        //dd( $this->articleRepository->findBy(['slug' => $slug]));
        return $this->renderForm('article/detail.html.twig', [
            'article' => $article,
            'formCommentaire' => $formCommentaire,
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

    #[Route('/articles/modification/{slug}', name: 'app_articles_edit', methods: ['GET', 'POST'])]
    public function update($slug, Request $request) : Response
    {
        $article = $this->articleRepository->findOneBy(['slug' => $slug]);

        // vérificationa article
        if (!$article) {
            return $this->redirectToRoute('app_articles');
        }

        // création du form
        $formArticle = $this->createForm(ArticleType::class, $article);

        // reconnait si le form à été soumis
        $formArticle->handleRequest($request);
        if($formArticle->isSubmitted() && $formArticle->isValid()){
            $this->articleRepository->add($article, true);
            return $this->redirectToRoute('app_articles_slug', ['slug' => $slug]);
        }

        // appel de la vue 'twig' permettant d'afficher le form
        return $this->renderForm('article/edit.html.twig', [
            'formArticle' => $formArticle,
        ]);
    }
}
