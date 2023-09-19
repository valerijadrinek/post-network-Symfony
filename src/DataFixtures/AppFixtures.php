<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use DateTime;
use App\Entity\MicroPost;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $UserPasswordHasher)
    {

    }
    public function load(ObjectManager $manager): void
    {
        $datetime = new DateTime();

        $user1 = new User();
        $user1->setEmail('test1@test.com');
        $user1->setPassword(
            $this->UserPasswordHasher->hashPassword($user1, 'secret123')
        );

        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('test2@test.com');
        $user2->setPassword(
            $this->UserPasswordHasher->hashPassword($user2, 'secret123')
        );
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('test3@test.com');
        $user3->setPassword(
            $this->UserPasswordHasher->hashPassword($user3, 'secret123')
        );
        $manager->persist($user3);
        


        $microPost1 = new MicroPost();
        $microPost1->setTitle('Welcome to Poland');
        $microPost1->setText('Lorem ipsum  Lorem ipsum dolor, sit amet consectetur adipisicing elit. Libero ducimus aliquid adipisci, qui ex tempore sit odit temporibus soluta non cum officiis optio nam ut dolorum fugit, atque nostrum minima.');
        $microPost1->setAuthor($user1);
        $microPost1->setCreatedAt($datetime);
        
        $manager->persist($microPost1);

        $microPost2 = new MicroPost();
        $microPost2->setTitle('Welcome to Germany');
        $microPost2->setText('Lorem ipsum  Lorem ipsum dolor, sit amet consectetur adipisicing elit. Libero ducimus aliquid adipisci, qui ex tempore sit odit temporibus soluta non cum officiis optio nam ut dolorum fugit, atque nostrum minima.');
        $microPost2->setAuthor($user2);
        $microPost2->setCreatedAt($datetime);
        
        $manager->persist($microPost2);

        $microPost3 = new MicroPost();
        $microPost3->setTitle('Welcome to USA');
        $microPost3->setText('Lorem ipsum  Lorem ipsum dolor, sit amet consectetur adipisicing elit. Libero ducimus aliquid adipisci, qui ex tempore sit odit temporibus soluta non cum officiis optio nam ut dolorum fugit, atque nostrum minima.');
        $microPost3->setAuthor($user3);
        $microPost3->setCreatedAt($datetime);
        
        $manager->persist($microPost3);

       


        $manager->flush();
    }
}
