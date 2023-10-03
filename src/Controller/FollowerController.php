<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FollowerController extends AbstractController
{
    #[Route('/follower/{id}', name: 'app_follow')]
    public function follow( User $userToFollow, UserService $service, Request $request): Response
    {
        /** @var User $currentUser  */
       $currentUser = $this->getUser();

       $service->setFollow($userToFollow, $currentUser);
       return $this->redirect($request->headers->get('referer')); //redirects to last visited page

    }

    #[Route('/unfollower/{id}', name: 'app_unfollow')]
    public function unfollow(User $userToUnfollow, UserService $service, Request $request): Response
    {
        /** @var User $currentUser  */
        $currentUser = $this->getUser();
        $service->setUnFollow($userToUnfollow, $currentUser);

        
 
        return $this->redirect($request->headers->get('referer')); //redirects to last visited page
 
    }
}
