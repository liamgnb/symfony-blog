<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for ($i=0; $i<20; $i++) {

            $auteur = null;
            if($faker->numberBetween(0,10)%3 != 0){
                $auteur = $this->getReference("auteur".$i);
            } else {
                $i--;
            }

            for ($j=0; $j<$faker->numberBetween(1,5); $j++) {
                $commentaire = new Commentaire();
                $article = $this->getReference("article".$faker->numberBetween(0,99));
                $commentaire->setContenu($faker->paragraphs(1, true))
                    ->setCreatedAt($faker->dateTimeBetween($article->getCreatedAt()->format("Y-m-d")))
                    ->setAuteur($auteur)
                    ->setArticle($article);
                $manager->persist($commentaire); // ordre INSERT
            }
        }
        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            AuteurFixtures::class,
            ArticleFixtures::class,
        ];
    }
}
