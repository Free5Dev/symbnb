<?php

namespace App\Form;

use App\Entity\User;
use App\Form\AppplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;



class RegistrationType extends ApplicationType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class, $this->getConfiguration("Prénom","Votre prénom..."))
            ->add('lastName',TextType::class, $this->getConfiguration("nom","Votre nom de famille..."))
            ->add('email',EmailType::class, $this->getConfiguration("Email","Votre adresse mail..."))
            ->add('picture',UrlType::class, $this->getConfiguration("Photo de profil","Url de votre avatar..."))
            ->add('hash',PasswordType::class, $this->getConfiguration("Mot de passe ","Saisissez un bon mots de passe..."))
            ->add('confirmPassword',PasswordType::class, $this->getConfiguration("Confirmez Mot de passe", "Veuillez confirmez le même Mots de passe"))
            ->add('introduction',TextType::class, $this->getConfiguration("Introduction","Présentez vous en quelque mots..."))
            ->add('description',TextareaType::class, $this->getConfiguration("Description Détaillée","C'est e moment de vous présenter en détails..."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
