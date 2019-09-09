<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\MailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils; 
use Symfony\Component\Mailer\MailerInterface;
use App\Services\Mailer;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="registration")
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            //  $message = (new \Swift_Message('Validation de l\'inscription'))
            //          ->setFrom('geoffroy.varlez@gmail.com')
            //          ->setTo('poupa_60@hotmail.fr')
            //          ->setCharset('utf-8')
            //          ->setBody(
            //          $this->renderView(
            //              'security/registration.html.twig',
            //              ['user'=>$user, 'form'=>$form]
            //          )         
            //     );

            //  $mailer->send($message);

          return $this->redirectToRoute('connexion', ['username' => $user->getUsername()]);

         }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login/{username}", name="connexion")
     */
    public function login(AuthenticationUtils $authenticationUtils, string $username = null): Response{


        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getlastUsername();

        if($username != null){
            $lastUsername = $username;
        }

        return $this->render('crypto/login.html.twig', [

            "last_username"=>$lastUsername,
            "error" => $error
        ]);
    }

    /**
     * @Route("/mot_de_passe_oublie", name="forgot_password")
     */
    public function forgotPassword(Mailer $mailer, Request $request, UserPasswordEncoderInterface $encoder, TokenGeneratorInterface $tokenGenerator, ObjectManager $manager){
    
        $form = $this->createFormBuilder()
        ->add('email', EmailType::class, [
            'constraints' => [
                new Email(),
                new NotBlank()
            ]
        ])
        ->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $user = $manager->getRepository(User::class)->loadUserByUsername($form->getData()['email']);

        if (!$user) {
            $request->getSession()->getFlashBag()->add('warning', "Cet email n'existe pas.");
            return $this->redirectToRoute("request_resetting");
        } 

        $user->setToken($tokenGenerator->generateToken());
        $user->setPasswordRequestedAt(new \Datetime());
        $manager->flush();

        $bodyMail = $mailer->createBodyMail('security/mail.html.twig', [
            'user' => $user
        ]);
        $mailer->sendMessage('geoffroy.varlez@gmail.com', $user->getEmail(), 'Renouvellement du mot de passe', $bodyMail);
        $request->getSession()->getFlashBag()->add('success', "Un mail va vous être envoyé afin que vous puissiez renouveller votre mot de passe. Le lien que vous recevrez sera valide 24h.");

        return $this->redirectToRoute("connexion");
    }
        
 
        return $this->render('security/forgot_password.html.twig', [

            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/Reinitialisation_MDP/{token}", name="reset_password")
     */
    // public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    // {
 
    //     if ($request->isMethod('POST')) {
    //         $entityManager = $this->getDoctrine()->getManager();
 
    //         $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
 
    //         if ($user === null) {
    //             $this->addFlash('danger', 'Token Inconnu');
    //             return $this->redirectToRoute('home');
    //         }
 
    //         $user->setResetToken(null);
    //         $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
    //         $entityManager->flush();
 
    //         $this->addFlash('notice', 'Mot de passe mis à jour');
 
    //         return $this->redirectToRoute('home');
    //     }else {
 
    //         return $this->render('security/reset_password.html.twig', ['token' => $token]);
    //     }
 
    // }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){}
}
