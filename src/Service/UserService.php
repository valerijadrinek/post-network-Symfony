<?php
namespace App\Service;
use App\Repository\UserRepository;
use App\Entity\UserProfile;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserService extends AbstractController{
    

  

    public function __construct(private ManagerRegistry $registry,
                                private EntityManagerInterface $entityManagerInterface,
                                private UserPasswordHasherInterface $userPasswordHasher,
                                private UserRepository $userRepository,
                                private SluggerInterface $slugger,
                                private EmailVerifier $emailVerifier){
                $this->emailVerifier = $emailVerifier;
    }



    //setting following
    public function setFollow ($userToFollow, $currentUser) {
        
       if($userToFollow->getId() !== $currentUser->getId()) {

        $currentUser->follow($userToFollow);
        $this->registry->getManager()->flush(); //becouse we don't change user table but inserting data into separate one, don't need register

       }

    }


    //setting unfollowing
    public function setUnFollow ($userToUnfollow, $currentUser) {
        if($userToUnfollow->getId() !== $currentUser->getId()) {
 
            $currentUser->unfollow($userToUnfollow);
            $this->registry->getManager()->flush(); 
    
           }
    }


    //form register handling
    public function formRegister($form, $user) :bool {


        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('accounts@micropost.com', 'MicroPost Symfony 6'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else here, like send an email
            return true;
        } else {
            return false;
        }
    }


    //profile handling
    public function profileSet($form, $user) {
         //validation and subbmition 
         if( $form->isSubmitted() &&  $form->isValid()) {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            //saving it
            $this->userRepository->add($user, true);

            return true;
         } else {
            return false;
         }
    }

    public function imageSet($form, $user) {

        if( $form->isSubmitted() &&  $form->isValid()) {
            //form file getting
            $profileImageFile = $form->get('profileImage')->getData();
            
            if($profileImageFile) {
                $originalFileName = pathinfo(
                    $profileImageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $safeFilename = $this->slugger->slug($originalFileName);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profileImageFile->guessExtension();

                

                try {

                    $profileImageFile->move(
                        $this->getParameter('profiles_directory'),//added to service.yaml file, here is only easy colled by this method
                        $newFilename
                    );

                } catch (FileException $e) {

                }
                //saving to db
               
                $profile = $user->getUserProfile() ?? new UserProfile();
                $profile->setImage($newFilename);
                $user->setUserProfile($profile);
                $this->userRepository->add($user, true);

               
            }
            return true;
        } else {
        return false;
        }


     }

}