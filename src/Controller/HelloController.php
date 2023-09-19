<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use App\Repository\UserProfileRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
 {

    private array $messages = [
        ['message' => 'Hello', 'created_at' => '2023/08/31'],
        ['message' => 'Hi there', 'created_at' => '2023/07/12'],
        ['message' => 'Bye', 'created_at' => '2021/05/12']
    ];

    #[Route('/', name:'app_index')]
    public function index(CommentRepository $comments, MicroPostRepository $posts) : Response
    {

        // $post = new MicroPost();
        // $post->setTitle('Hello');
        // $post->setText('Hello');
        // $crat = new DateTime();
        // $post->setCreatedAt($crat);
        // $post = $posts->find(6);
        // $comment = $post->getComments()[0];
        // $post->removeComment($comment);
        // $posts->add($post, true);

        // $comment = new Comment();
        // $comment->setText('Hello you too');
        // $comment->setPost($post);
        // //$posts->add($post, true);
        // $comments->add($comment, true);
        // $user = new User();
        // $user->setEmail('dummyemail@rmail.com');
        // $user->setPassword('12345');


        // $userProfile = new UserProfile();
        // $userProfile->setUser($user);
        // $userProfile->setName('New User');
        // $profiles->add($userProfile, true);

        //  $profile = $profiles->find(1);
        //  $profiles->remove($profile, true);
        

        return $this->render(
            'hello/index.html.twig',
            [
            'messages' => $this->messages,
            'limit' => 3
            ]
        );

    }

    #[Route('/messages/{id<\d+>}', name:'app_show_one')]
    public function showOne(int $id) : Response
    {
       return $this->render(
         'hello/show_one.html.twig',
         [
            'message' => $this->messages[$id]
         ]
       );
       //return new Response($this->messages[$id]);
    }
}