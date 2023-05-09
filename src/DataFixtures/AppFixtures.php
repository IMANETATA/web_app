<?php

namespace App\DataFixtures;
use App\Entity\Ingredient;
use App\DataFixtures\Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
//use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Doctrine\Persistence\ObjectManager;
use Generator;

class AppFixtures extends Fixture
{
   
    public function load(ObjectManager $manager): void
    {

        for($i=0;$i<50;$i++){
            $Ingredient= new Ingredient();
            $Ingredient->setName("clavier")
            ->setPrice(mt_rand(0,100));
            $manager->persist($Ingredient);
        }
        $manager->flush();
    }
}
