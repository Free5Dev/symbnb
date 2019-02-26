<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Role;

// use Cocur\Slugify\Slugify;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){

        $this->encoder= $encoder;

    }
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create("fr_FR");
        // pourv les roles  admin
        $adminRole= new Role();
        $adminRole->setTitle("ROLE_ADMIN");
        $manager->persist($adminRole);
        // insertion d'un utiliateur avec le role admin
        $adminUser= new User();
        $adminUser->setFirstName("Thierno")
              ->setLastName("Soumah")
              ->setEmail("tsoumahjob@gmail.com")
              ->setIntroduction($faker->sentence())
              ->setDescription('<p>'.join('<p></p>',$faker->paragraphs(2)).'</p>')
              ->setHash($this->encoder->encodePassword($adminUser, 'password'))
              ->setPicture('https://or-formation.com/uploads/img/produits/44.png')
              ->addUserRole($adminRole);
        $manager->persist($adminUser);
        // pour les utilisateurs
        $users =[];
        $genres=['male','female'];
        for($i=1; $i<=10; $i++){
            $user= new User();

            $genre=$faker->randomElement($genres);
            $picture='https://randomuser.me/api/portraits/';
            $pictureId=$faker->numberBetween(1,99).'.jpg';
            $picture .=($genre =='male' ? 'men/' : 'women/' ).$pictureId;
            $hash=$this->encoder->encodePassword($user, 'password');
            $user->setFirstName($faker->firstName)
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>'.join('<p></p>',$faker->paragraphs(2)).'</p>')
                 ->setHash($hash)
                 ->setPicture($picture);
            $manager->persist($user);
            $users[]= $user;
        }
        // pour les annonces 
        // $slugify= new Slugify();
        for($i=1; $i<=30; $i++){
            $ad= new Ad();

            $title=$faker->sentence();
            // $slug=$slugify->slugify($title); 
            $coverImage=$faker->imageUrl(1000, 350);
            $introduction=$faker->paragraph(2);
            $content='<p>'.join('<p></p>',$faker->paragraphs(5)).'</p>';
            $user=$users[mt_rand(0, count($users)-1)];
            $ad->setTitle($title)
            //    ->setSlug($slug)
               ->setIntroduction($introduction)
               ->setContent($content)
               ->setPrice(mt_rand(40, 200))
               ->setCoverImage($coverImage)
               ->setRooms(mt_rand(1,5))
               ->setAuthor($user);
            $manager->persist($ad);
            // ajout des images entre deux et cinq
            for($j=1; $j<=mt_rand(2,5); $j++){
                $image= new Image();
                $image->setUrl($faker->imageUrl()) 
                      ->setCaption($faker->sentence())
                      ->setAd($ad);
                $manager->persist($image);  
            }
        }
        $manager->flush();
    }
}
