<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\ProfileImageType;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class SettingProfileController extends AbstractController
{
    #[Route('/setting/profile', name: 'app_setting_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    
    public function profile(
              Request $request,
              UserRepository $users
    ): Response
    {
        /** @var User $user */
        //getting the user
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();


        //creating form
        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        //validation and subbmition 
        if( $form->isSubmitted() &&  $form->isValid()) {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            //saving it
            $users->add($user, true);
            //adding flash messages
            $this->addFlash(
                'success', 'Your user profile settings were saved'
            );
            //redirect
            return $this->redirectToRoute('app_setting_profile');
        }

        return $this->render('setting_profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/setting/profile-image', name: 'app_setting_profile_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(Request $request, SluggerInterface $slugger, UserRepository $users) : Response 
    {
        $form = $this->createForm(ProfileImageType::class);
        /** @var User $user */
        $user = $this->getUser();
        $form->handleRequest($request);

        //validation and subbmition 
        if( $form->isSubmitted() &&  $form->isValid()) {
            //form file getting
            $profileImageFile = $form->get('profileImage')->getData();
            
            if($profileImageFile) {
                $originalFileName = pathinfo(
                    $profileImageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $safeFilename = $slugger->slug($originalFileName);
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
                $users->add($user, true);

                $this->addFlash('success', 'Your profile image was updated.');

                return $this->redirectToRoute('app_setting_profile_image');
                
                
               
            }
        }
           
        
        
        return $this->render('setting_profile/profile_image.html.twig', [
            'form' => $form->createView(),
        ]);

}

}
