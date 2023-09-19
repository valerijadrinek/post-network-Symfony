<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FollowerController extends AbstractController
{
    #[Route('/follower/{id}', name: 'app_follow')]
    public function follow( User $userToFollow, ManagerRegistry $register, Request $request): Response
    {
        /** @var User $currentUser  */
       $currentUser = $this->getUser();

       if($userToFollow->getId() !== $currentUser->getId()) {

        $currentUser->follow($userToFollow);
        $register->getManager()->flush(); //becouse we don't change user table but inserting data into separate one don't need register

       }

       return $this->redirect($request->headers->get('referer')); //redirects to last visited page

    }

    #[Route('/unfollower/{id}', name: 'app_unfollow')]
    public function unfollow(User $userToUnfollow, ManagerRegistry $register, Request $request): Response
    {
        /** @var User $currentUser  */
        $currentUser = $this->getUser();

        if($userToUnfollow->getId() !== $currentUser->getId()) {
 
         $currentUser->unfollow($userToUnfollow);
         $register->getManager()->flush(); //becouse we don't change user table but inserting data into separate one don't need register
 
        }
 
        return $this->redirect($request->headers->get('referer')); //redirects to last visited page
 
    }
}
