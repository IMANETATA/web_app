<?php

namespace App\DataFixtures;
use Generator;
use App\Entity\User;
use App\Entity\Recipe;
//use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use App\Entity\Ingredient;
use App\DataFixtures\Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    
    public function load(ObjectManager $manager): void
    {
        
        //users
        $users=[];

        for ($i=0;$i<10;$i++){
            $user=new User();
            $user->setFullNmae('name')
            ->setPseudo(mt_rand(0,1)=== 1 ? 'pseudo': null )
            ->setEmail('email'.$i.'@symrecip.fr')
            ->setRoles(['ROLE_USER'])
            ->setPlainPassword('password');
            $users[]=$user;
            $manager->persist($user);
        }

        //ingredients
        $ingredients=[];
        for($i=0;$i<50;$i++){
            $ingredient= new Ingredient();
            $ingredient->setName("fraise".$i)
            ->setPrice(mt_rand(0,100));
          //  ->setUser($users[mt_rand(0,count($users)-1)]);

            $ingredients[]=$ingredient;
           $manager->persist($ingredient);
        }

        //recipes
    for ($j=0;$j<25;$j++){
        $recipe= new Recipe();
       $recipe->setName("recipe")
            ->setPrice(mt_rand(0,1)==1? mt_rand(1,1000):null)
            ->setTime(mt_rand(0,1)==1? mt_rand(1,1440):null)
            ->setNbPeople(mt_rand(0,1)==1? mt_rand(1,50):null)
            ->setDifficulty(mt_rand(0,1)==1? mt_rand(1,5):null)
            ->setDescription("description ..")
            ->setIsFavorite(mt_rand(0,1)==1? true:false)
            ->setUser($users[mt_rand(0,count($users)-1)]);
            for ($k=0;$k<mt_rand(5,15);$k++){
                $recipe->addIngredient($ingredients[mt_rand(0 , count($ingredients)-1)]);
            }

            
        $manager->persist($recipe);
    }


        $manager->flush();
    } 
}
