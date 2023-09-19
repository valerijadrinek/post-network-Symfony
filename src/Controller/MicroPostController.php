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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MicroPostController extends AbstractController
{

    //showing all posts
   #[Route('/micro-post', name: 'app_micro_post')]
   public function index(MicroPostRepository $posts): Response
   {

       return $this->render('micro_post/index.html.twig', [
           'posts' => $posts->findAllWithComments(),
       ]);
   }


  //showing Top Liked posts
    #[Route('/micro-post/top-liked', name: 'app_micro_post_topliked')]
    public function topLiked(MicroPostRepository $posts): Response
    {

        return $this->render('micro_post/top_liked.html.twig', [
            'posts' => $posts->findAllWithMinLikes(2)
        ]);
    }

   

    //showing posts from persons that user follows
    #[Route('/micro-post/follows', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostRepository $posts): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();


        return $this->render('micro_post/follows.html.twig', [
            'posts' => $posts->findAllByAuthorsThatUserFollows(
               $currentUser->getFollows()
            ),
        ]);
    }

   //showing one post
    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post) : Response
    {
     
      return $this->render('micro_post/show.html.twig', [
        'post' => $post,
    ]);
    }


    #[Route('/micro-post/add', name:'app_micro_post_add', priority:2)]
    #[IsGranted('ROLE_WRITER')]
    public function add(Request $request, MicroPostRepository $posts) : Response 
    {
      //denying access like this or throught attribute
      // $this->denyAccessUnlessGranted(
      //    'IS_AUTHENTICATED_FULLY', now ROLE_WRITER-meaning verified by email
      // );

      
       $microPost = new MicroPost();
       //form
       $form = $this->createForm(MicroPostType::class, $microPost);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid())
       {
        

        $post = $form->getData();
        $post->setAuthor($this->getUser());
        $posts->add($post, true);

        //Add a flash message
        $this->addFlash('success', 'Your post has been added'); 

        return $this->redirectToRoute('app_micro_post');
        
       }

       return $this->renderForm('micro_post/add.html.twig', 
    [
       'form' => $form 
    ]);

    }

    #[Route('/micro-post/{post}/edit', name:'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, MicroPostRepository $posts) : Response 
    {
       
       $form = $this->createForm(MicroPostType::class, $post);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid())
       {

        

        $post = $form->getData();
        $posts->add($post, true);

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

    #[Route('/micro-post/{post}/comment', name:'app_micro_post_comment')]
    #[IsGranted('ROLE_COMMENTER')]
    public function addComment(MicroPost $post, Request $request, CommentRepository $comments) : Response 
    {
       $comment = new Comment();
       $form = $this->createForm(CommentType::class, $comment);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid())
       {

        $datetime = new DateTime();

        $comment = $form->getData();
        $comment->setPost($post);
        $comment->setAuthor($this->getUser());
        
        $comments->add($comment, true);

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
