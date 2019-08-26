<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils; 
use Symfony\Component\HttpFoundation\Response;

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

            return $this->redirectToRoute('connexion');

        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
            'title' =>'Cryptocheck'
        ]);
    }

    /**
     * @Route("/login", name="connexion")
     */
    public function login(AuthenticationUtils $authent): Response{

        $error = $authent->getLastAuthenticationError();

        return $this->render('crypto/login.html.twig', [
            "title" => "Cryptocheck",
            "error" => $error
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){}
}
