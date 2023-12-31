<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\MicroPost;
use App\Service\MicroPostService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function show( MicroPost $post, MicroPostService $service): Response
    {
        $profileUser = $post->getAuthor();
        
        $posts = $service->postsByProfileAuthor($profileUser);
        return $this->render('profile/show.html.twig', [
            'user' => $profileUser,
            'posts' => $posts
        ]);
    }

    #[Route('/profile/{id}/follows', name: 'app_profile_follows')]
    public function follows(User $user): Response
    {
        return $this->render('profile/follows.html.twig', [
            'user' => $user
            
        ]);
    }

    #[Route('/profile/{id}/followers', name: 'app_profile_followers')]
    public function followers(User $user): Response
    {
        return $this->render('profile/followers.html.twig', [
            'user' => $user
        ]);
    }
}
