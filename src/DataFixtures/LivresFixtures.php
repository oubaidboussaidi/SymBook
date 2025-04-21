<?php

namespace App\DataFixtures;

use App\Entity\Livres;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LivresFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Use Faker for random data

        for ($i = 1; $i <= 20; $i++) { // Add 20 books
            $livre = new Livres();
            $livre->setTitre($faker->sentence(3));
            $livre->setSlag($faker->slug);
            $livre->setImage('https://picsum.photos/200/300'); // Random book cover image
            $livre->setResume($faker->text(200));
            $livre->setEditeur($faker->company);
            $livre->setDateEdition($faker->dateTimeBetween('-10 years', 'now'));
            $livre->setPrix($faker->randomFloat(2, 5, 50)); // Random price between 5 and 50€

            $manager->persist($livre);
        }

        $manager->flush();
    }
    
}
