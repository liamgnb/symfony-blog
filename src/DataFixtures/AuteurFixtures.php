<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Auteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuteurFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for ($i=0; $i<20; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($faker->lastName())
                    ->setPrenom($faker->firstName())
                    ->setPseudo($faker->userName());
            $this->addReference("auteur".$i, $auteur);
            $manager->persist($auteur); // ordre INSERT
        }

        $manager->flush();
    }
}
