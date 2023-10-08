<?php
namespace App\Service;

use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MicroPostService extends AbstractController {

    
    public function __construct(private MicroPostRepository $repository, 
                                private CommentRepository $commentRepository) {
       
    }

    //all posts for index page
    public function allPosts () {

        $posts = $this->repository->findAllWithComments();
        return $posts;
    }

    //posts for top liked page
    public function topLikedPosts() {

        $posts = $this->repository->findAllWithMinLikes(2);
        return $posts;
    }


    //posts from people who you follow page
    public function postsFromFollows($follows) {

        $posts = $this->repository->findAllByAuthorsThatUserFollows($follows);
        return $posts;
    }

    //posts by Author
    public function postsByProfileAuthor($profileUser) {

        $posts = $this->repository->findAllByAuthor($profileUser);
        return $posts;
    }


    //adding a new post form
    public function formAdd($form) : bool {
        if ($form->isSubmitted() && $form->isValid())
        {
         
 
         $post = $form->getData();
         $post->setAuthor($this->getUser());
         $this->repository->add($post, true);
 
         return true;
        } else {
            return false;
        }
    }


    //editing an existing post page 
    public function formEdit($form) : bool {

        if ($form->isSubmitted() && $form->isValid())
        {
 
         $post = $form->getData();
         $this->repository->add($post, true);

         return true;
 
        } else {
            return false;
        }
    }

    //add comment form page 
    public function formComment($form, $post) : bool {

        if ($form->isSubmitted() && $form->isValid())
        {
 
         $comment = $form->getData();
         $comment->setPost($post);
         $comment->setAuthor($this->getUser());
         
         $this->commentRepository->add($post, true);

         return true;
        } else {
            return false;
        }
}

    public function likePost($post) {
          
        $this->repository->add($post, true);
        }

}