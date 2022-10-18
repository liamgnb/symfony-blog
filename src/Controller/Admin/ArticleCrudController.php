<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnDetail()
                ->hideOnForm(),
            TextField::new('titre'),
            TextEditorField::new('contenu')
                ->hideOnIndex()
                ->setSortable(false),
            AssociationField::new('categorie')
                ->setRequired(false),
            DateTimeField::new('createdAt', 'Date de création')
                ->hideOnForm(),
            TextField::new('slug')
                ->hideOnForm()
                ->hideOnDetail()
                ->hideOnIndex(),
            BooleanField::new('estPublie', 'Publié'),
            ArrayField::new('commentaires')->onlyOnDetail(),
            CollectionField::new('commentaires')->onlyOnForms()->setEntryType(CommentaireType::class),
        ];
    }

    // redéfinir la méthode persistEntity : appelé lors de la création de l'article en BDD
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // vérrifier qu' $entityInstance soit bien une isntance d'Article
        if (! $entityInstance instanceof Article) return;

        $entityInstance->setCreatedAt(new \DateTime());
        $entityInstance->setSlug($this->slugger->slug($entityInstance->getTitre())->lower());

        parent::persistEntity($entityManager, $entityInstance);

    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setPageTitle(Crud::PAGE_INDEX, 'Liste des articles');
        $crud->setPageTitle(Crud::PAGE_NEW, 'Ajouter un article');
        $crud->setPageTitle(Crud::PAGE_DETAIL, 'Detail');
        $crud->setPageTitle(Crud::PAGE_EDIT, 'Modification');
        $crud->setPaginatorPageSize(10);
        $crud->setDefaultSort(['createdAt' => 'DESC']);
        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::NEW,
            function (Action $action) {
                $action->setLabel('Ajouter un article');
                $action->setIcon('fas-solid fa-plus');
                return $action;
            }
        );
        $actions->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN,
            function (Action $action) {
                $action->setLabel('Ajouter');
                $action->setIcon('fa fa-check');
                return $action;
            }
        );
        $actions->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        return $actions;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add('titre')
                ->add('createdAt')
                ->add('categorie');
        return $filters;
    }


}
