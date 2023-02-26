<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{    
    #[Route('/register', name: 'app_register', priority: 2)]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        JWTService $jwt,
        SendMailService $mail,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            //generating users JWT
            //creating Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            //creating Payload
            $payload = [
                'user_id' => $user->getId(),
                'user_email' => $user->getEmail(),
            ];
            //generating token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send(
                'no-reply@floreil.net',
                $user->getEmail(),
                'Activation votre compte sur le site e-commerce',
                'register',
                [
                    'user' => $user,
                    'token' => $token
                ]
                // alt form for the last parameter:
                //compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            //return $this->redirectToRoute('app_main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/verify/{token}', name: 'app_verify_email', priority: 2)]
    public function verifyUserEmail(
        $token,
        JWTService $jwt,
        UserRepository $users,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->checkSignature($token, $this->getParameter('app.jwtsecret'))) {
            $payload = $jwt->getPayload($token);
            $user = $users->find($payload['user_id']);
        } else {
            $this->addFlash('danger', "Le token est invalide ou a expiré");
            return $this->redirectToRoute('app_login');
        }

        if ($user && !$user->isVerified()) {
            $user->setIsVerified(true);
            $entityManager->flush($user);
            $this->addFlash('success', "Votre compte est activé!");
            return $this->redirectToRoute('app_main');
        }

        //if token isn't valid or changed
        $this->addFlash('danger', "Le token est invalide ou a expiré");
        return $this->redirectToRoute('app_login');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/resendverif', name: 'app_resend_verify', priority: 2)]
    public function resendVerif(JWTService $jwt, SendMailService $email): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', "Vous devez être connecté pour accéder à cette page!");
            return $this->redirectToRoute('app_login');
        }

        if ($user->isVerified()) {
            $this->addFlash('warning', "Votre compte est déjà activé.");
            return $this->redirectToRoute('app_main');
        }

        $header = [
            "typ" => "JWT",
            "alg" => "SH256"
        ];
        $payload = [
            "user_id" => $user->getId(),
            "user_email" => $user->getEmail()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $email->send(
            'no-reply@floreil.fr',
            $user->getEmail(),
            'Activation votre compte sur le site e-commerce',
            'register',
            [
                'user' => $user,
                'token' => $token
            ]
        );
        $this->addFlash('success', "Email de vérification envoyé.");
        return $this->redirectToRoute('app_main');
    }
}
