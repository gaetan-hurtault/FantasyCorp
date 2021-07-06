<?php

namespace App\Form;

use App\Entity\Register;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,[
                'label' => 'Prénom :'
            ])
            ->add('lastName',TextType::class,[
                'label' => 'Nom :'
            ])
            ->add('mail',EmailType::class,[
                'label' => 'Email :'
            ])
            ->add('password',PasswordType::class,[
                'label' => 'Mot de Passe :'
            ])
            ->add('passwordVerif',PasswordType::class,[
                'label' => 'Vérification du Mot de Passe :'
            ])
            ->add('adress',TextareaType::class,[
                'label' => 'Adresse :'
            ])
            ->add('city',TextType::class,[
                'label' => 'Ville :'
            ])
            ->add('codePostal',TextType::class,[
                'label' => 'Code Postal :'
            ])
            ->add('country',TextType::class,[
                'label' => 'Pays :'
            ])
            ->add('phoneNumber',TextType::class,[
                'label' => 'Numéro de Téléphone :'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Register::class,
        ]);
    }
}
