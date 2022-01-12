<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Commune;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       $faker = \Faker\Factory::create('fr_FR'); 
      

       for ($i=1; $i < 3 ; $i++) { 
          $category = new Category();
          $category->setLibelle($faker->sentence());
          $category->setArchived(1);
          $manager->persist($category);

          for ($j=1; $j < 10 ; $j++) { 
              $product = new Product();
              $product->setLibelle($faker->sentence())
                      ->setArchived($faker->numberBetween($min = 0, $max = 1))
                      ->setDescription($faker->paragraph($nbSentences = 3, $variableNbSentences = true))
                      ->setPhoto($faker->imageUrl($width = 640, $height = 480,  'cats', true, 'Faker'))
                      ->setCategory($category)
                      ->setCreatedAt(new \DateTime())
                      ->setPrice($faker->numberBetween($min = 1000, $max = 9000));
            
            $manager->persist($product);
          }
       }

       for ($k=1; $k < 10; $k++) { 
             $commune = new Commune();
             $commune->setLibelle($faker->sentence())
                     ->setTarif($faker->numberBetween($min = 500, $max = 5000))
                     ->setMontantMax($faker->numberBetween($min = 20000, $max = 50000))
                     ->setCreatedAt(new \DateTime());
            $manager->persist($commune);
       }

        $manager->flush();
    }
}
