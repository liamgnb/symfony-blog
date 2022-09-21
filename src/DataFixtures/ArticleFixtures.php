<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // Faker
        $faker = Factory::create("fr_FR");

        for ($i=0; $i<50; $i++) {
            $article = new Article();
            $article->setTitre($faker->words($faker->numberBetween(3,10), true))
                    ->setContenu($faker->paragraphs(3, true))
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $manager->persist($article); // ordre INSERT
        }


        // envoie à la BDD
        $manager->flush();
    }
}
