<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use App\Service\MicroPostService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MicroPostController extends AbstractController
{

    //showing all posts
   #[Route('/{_locale}/', name: 'app_micro_post')]
   public function index(MicroPostService $service): Response
   {
        $posts = $service->allPosts();

        return $this->render('micro_post/index.html.twig', [
           'posts' => $posts,
       ]);
   }


  //showing Top Liked posts
    #[Route('/{_locale}/top-liked', name: 'app_micro_post_topliked')]
    public function topLiked(MicroPostService $service): Response
    {
        
        $posts = $service->topLikedPosts();

        return $this->render('micro_post/top_liked.html.twig', [
            'posts' => $posts
        ]);
    }

   

    //showing posts from persons that user follows
    #[Route('/{_locale}/follows', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostService $service): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $follows= $currentUser->getFollows();

        $posts = $service->postsFromFollows($follows);
        return $this->render('micro_post/follows.html.twig', [
            'posts' => $posts
        ]);
    }

   //showing one post
    #[Route('/{_locale}/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post) : Response
    {
     
      return $this->render('micro_post/show.html.twig', [
        'post' => $post,
    ]);
    }


    #[Route('/{_locale}/add', name:'app_micro_post_add', priority:2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request, MicroPostService $service) : Response 
    {
      //denying access like this or throught attribute
      // $this->denyAccessUnlessGranted(
      //    'IS_AUTHENTICATED_FULLY', now ROLE_WRITER-meaning verified by email
      // );

      
       $microPost = new MicroPost();
       //form
       $form = $this->createForm(MicroPostType::class, $microPost);

       $form->handleRequest($request);

       $added = $service->formAdd($form);
       if($added) {
        //Add a flash message
        $this->addFlash('success', 'Your post has been added'); 
 
        return $this->redirectToRoute('app_micro_post');
        
       }

       return $this->renderForm('micro_post/add.html.twig', 
    [
       'form' => $form 
    ]);

    }

    #[Route('/{_locale}/{post}/edit', name:'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, MicroPostService $service) : Response 
    {
       
       $form = $this->createForm(MicroPostType::class, $post);

       $form->handleRequest($request);

       $edited = $service->formEdit($form);
       
       if($edited) {
        //Add a flash message
        $this->addFlash('success', 'Your post has been editted'); 

        return $this->redirectToRoute('app_micro_post');
        //Redirect
       }

       return $this->renderForm('micro_post/edit.html.twig', 
    [
       'form' => $form,
       'post' => $post,
       
    ]);

    }

    #[Route('/{_locale}/{post}/comment', name:'app_micro_post_comment')]
    #[IsGranted('ROLE_COMMENTER')]
    public function addComment(MicroPost $post, Request $request, MicroPostService $service) : Response 
    {
       $comment = new Comment();
       $form = $this->createForm(CommentType::class, $comment);

       $form->handleRequest($request);

       $commented = $service->formComment($form, $post);

       if($commented) {

        //Add a flash message
        $this->addFlash('success', 'Your comment has been editted'); 

        return $this->redirectToRoute('app_micro_post_show',
            [
               'post' => $post->getId()
            ]);
        
       }

       return $this->renderForm('micro_post/comment.html.twig', 
    [
       'form' => $form, 
       'post' => $post
    ]);

    }
}
