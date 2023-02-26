<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileImageFormType;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Form\ProfileSettingsFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', priority: 2)]
    
    public function index(OrderRepository $orders): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /**  */
        $userOrders = $orders->findByUser($user->getId(), ['created_at' => 'DESC']);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'orders' => $userOrders,
        ]);
    }

    #[Route('/profile/settings', name: 'app_profile_settings', priority: 2)]
    public function settings(Request $request, UserRepository $users, SluggerInterface $slugger): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userInfoForm = $this->createForm(ProfileSettingsFormType::class);
        $userInfoForm->handleRequest($request);

        if ($userInfoForm->isSubmitted() && $userInfoForm->isValid()) {
            $user->setMobTel($userInfoForm->get('mobTel')->getData());
            $user->setCodeZIP($userInfoForm->get('codeZIP')->getData());
            $user->setCity($userInfoForm->get('city')->getData());
            $user->setStreet($userInfoForm->get('street')->getData());
            $user->setBuildNum($userInfoForm->get('buildNum')->getData());

            $users->save($user, true);

            $this->addFlash('success', 'Your contact information was updated.');
            return $this->redirectToRoute('app_profile_settings');
        }

        $avatarForm = $this->createForm(ProfileImageFormType::class);
        $avatarForm->handleRequest($request);

        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            $avatarImageFile = $avatarForm->get('profileImage')->getData();

            // check if there is an image file already attached to this user and if yes - delete it from project folder
            $fileToDelete = $this->getParameter('profiles_directory') . '/' . $user->getAvatarImage();
            if (is_file($fileToDelete))
                unlink($fileToDelete);

            if ($avatarImageFile) {
                $originalFileName = pathinfo(
                    $avatarImageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $safeFilename = $slugger->slug($originalFileName);
                $newFileName = $safeFilename . '-' . uniqid() . '.' . $avatarImageFile->guessExtension();

                try {
                    $avatarImageFile->move(
                        $this->getParameter('profiles_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                }
                $user->setAvatarImage($newFileName);
                $users->save($user, true);

                $this->addFlash('success', 'Your profile image was updated.');
                return $this->redirectToRoute('app_profile_settings');
            }
        }

        return $this->render(
            'profile/settings.html.twig',
            [
                'user' => $user,
                'userInfoForm' => $userInfoForm,
                'userAvatarForm' => $avatarForm
            ]
        );
    }
}
