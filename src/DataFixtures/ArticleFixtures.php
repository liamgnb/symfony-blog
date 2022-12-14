<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {

        // Faker
        $faker = Factory::create("fr_FR");

        for ($i=0; $i<100; $i++) {
            $article = new Article();
            $article->setTitre($faker->words($faker->numberBetween(3,10), true))
                    ->setContenu($faker->paragraphs(3, true))
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setSlug($this->slugger->slug($article->getTitre())->lower())
                    ->setCategorie($this->getReference("categorie".$faker->numberBetween(0,8)))
                    ->setEstPublie($faker->boolean(90));
            $this->addReference("article".$i, $article);
            $manager->persist($article); // ordre INSERT
        }


        // envoie à la BDD
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
          CategorieFixtures::class,
        ];
    }
}
