<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\MicroPost;
use App\Entity\UserProfile;
use App\Form\ProfileImageType;
use App\Form\UserProfileType;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SettingProfileController extends AbstractController
{
    #[Route('/setting/profile', name: 'app_setting_profile', priority:2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
  
    
    public function profile(
              Request $request,
              UserService $service
            ): Response
    {
        /** @var User $user */
        //getting the user
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();


        //creating form
        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        $profileSet = $service->profileSet($form, $user);
        
        //adding flash messages
        if($profileSet) {

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

    #[Route('/setting/profile-image', name: 'app_setting_profile_image', priority:2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(Request $request, UserService $service) : Response 
    {
        $form = $this->createForm(ProfileImageType::class);
        /** @var User $user */
        $user = $this->getUser();
        $form->handleRequest($request);

        $imageSet = $service->imageSet($form, $user);
        
                
            if($imageSet) {
                $this->addFlash('success', 'Your profile image was updated.');

                return $this->redirectToRoute('app_setting_profile_image');
                
            }
        
        
        return $this->render('setting_profile/profile_image.html.twig', [
            'form' => $form->createView(),
        ]);

}

}
