<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // génération d'une URL pour acceder au crud des article
        return $this->redirect($adminUrlGenerator->setController(ArticleCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration LRR-Blog');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fas fa-home-lg-alt');
        yield MenuItem::subMenu('Article', 'fas fa-filter')->setSubItems([
            MenuItem::linkToCrud('Liste', 'fas fa-list', Article::class)
                ->setAction(Crud::PAGE_INDEX)
                ->setDefaultSort(['createdAt' => 'DESC']),
            MenuItem::linkToCrud('Ajouter', 'fas-solid fa-plus', Article::class)
                ->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu('Catégories', 'fas fa-filter')->setSubItems([
            MenuItem::linkToCrud('Liste', 'fas fa-list', Categorie::class)
                ->setAction(Crud::PAGE_INDEX),
            MenuItem::linkToCrud('Ajouter', 'fas-solid fa-plus', Categorie::class)
                ->setAction(Crud::PAGE_NEW),
        ]);
    }
}
