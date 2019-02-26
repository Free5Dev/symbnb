<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AccountController extends AbstractController
{
    /**
     * Permet de se connecter
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        // pour affiche un msg d'erreur d'authentification
        $error= $utils->getLastAuthenticationError();
        // pour recup le dernier nom d'utilisateur de login
        $username=$utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'hasError'=>$error!==null,
            'username'=>$username
        ]);
    }
    /**
     * Permet de se deconnecter
     *@Route("/logout", name="account_logout")
     * @return Response
     */
    public function logout(){
        //ya rien c'est symfony qui gere la deconnexion
    }
    /**
     * Permet d'afficher un formulaire d'inscription
     *@Route("/register", name="account_register")
     * @return void
     */
    public function register(Request $request, ObjectManager $manager,UserPasswordEncoderInterface $encoder ){
        $user = new User();
        $form=$this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $hash=$encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();
            // ajout de message flash
            $this->addFlash(
                'success',
                "Bravo vous êtes bien inscrit sur mon  site !"
            );
            return $this->redirectToRoute("account_login");
        }
        return $this->render('account/registration.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *@Route("/account/profile", name="account_profile")
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager){
        // pour savoir l'utilisateur en cours
        $user= $this->getUser();
        $form=$this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();
            // message flash
            $this->addFlash(
                'success',
               "Les données du profil ont été enresistrée avec success"
            );
        }
        return $this->render('account/profil.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     *Permet de modifier le password
     *@Route("/account/password-update", name="account_password")
     *isGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request, Objectmanager $manager, UserPasswordEncoderInterface $encoder){
        $passwordUpdate= new PasswordUpdate();
        $form=$this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isvalid()){
            //si l'utilisateur est connecté
            $user=$this->getuser();
            // 1. verifier que le oldPassword soit le même que celui de password User ds la bdd
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
                //gerer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel ! "));
            }else{
                //recupere le nouveau password
                $newPassword= $passwordUpdate->getNewPassword();
                //l'encoder avec le bcrypt
                $hash=$encoder->encodePassword($user, $newPassword);
                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();
                // message flash
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );

                return $this->redirectToRoute("homepage");
            }
        }
        return $this->render('account/password.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * Permet d'affiché le profil d'un utilisateur connecté
     *@Route("/account", name="account_index")
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount(){
        return $this->render('user/index.html.twig',[
            'user'=>$this->getuser()
        ]);
    }
}
