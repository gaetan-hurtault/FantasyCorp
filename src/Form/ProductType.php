<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Editor;
use App\Entity\Languages;
use App\Entity\Product;
use App\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class,[
            'label' => "Nom du Produit : "
        ])
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'label' => "Catégorie : ",
            'choice_label' => 'name',])
        ->add('editor', EntityType::class, [
            'class' => Editor::class,
            'choice_label' => 'name',
            'label' => 'Editeur : ',
            'required' => false])
        ->add('price', NumberType::class,[
            'label' => "Prix de Vente TTC : "
        ])
        ->add('pricePurchasing', NumberType::class,[
            'label' => "Prix d'Achat HT : "
        ])
        ->add('height', NumberType::class,[
            'attr' => [
                'placeholder' => 'hauteur'
            ]
        ])
        ->add('length', NumberType::class,[
            'attr' => [
                'placeholder' => 'longueur'
            ]
        ])
        ->add('width', NumberType::class,[
            'attr' => [
                'placeholder' => 'largeur'
            ]
        ])
        ->add('weight', NumberType::class,[
            'label'=> 'Poids (en g) : '
        ])
        ->add('sellerParticular', CheckboxType::class,[
            'label'=> 'Acheté à un particulier',
            'required' => false
        ])
        ->add('ageMin', NumberType::class,[
            'required' => false,
            'label' => "Âge Minimum : "
        ])
        ->add('playerNumberMin', NumberType::class,[
            'required' => false,
            'attr' => [
                'placeholder' => 'Min'
            ]
        ])
        ->add('playerNumberMax', NumberType::class,[
            'required' => false,
            'attr' => [
                'placeholder' => 'Max'
            ]
        ])
        ->add('timePlayingMin', ChoiceType::class, [
            'required' => false,
            'choices'  => [
                'Moins de 30 Minutes' => 0,
                '30 Minutes' => 30,
                '1 Heure' => 60,
                '2 heures' => 120,
                'plus de 3 heures' => 180
            ],])
        ->add('timePlayingMax', ChoiceType::class, [
            'required' => false,
            'choices'  => [
                'Moins de 30 Minutes' => 0,
                '30 Minutes' => 30,
                '1 Heure' => 60,
                '2 heures' => 120,
                'plus de 3 heures' => 180
            ],])
        ->add('themes', EntityType::class,[
            'class' => Theme::class,
            'label' => "Thèmes : ",
            'required' => false,
            'multiple' => true,
            'choice_label' => 'name',
        ])
        ->add('language', EntityType::class,[
            'class' => Languages::class,
            'label' => "Langues : ",
            'required' => false,
            'multiple' => true,
            'choice_label' => 'name',
        ])
        ->add('images', FileType::class,[
            'label' => true,
            'multiple' => true,
            'mapped' => false,
            'required' => false
        ])
        ->add('pictureFirst', FileType::class,[
            'label' => false,
            'multiple' => false,
            'mapped' => false,
            'required' => false
        ])
        ->add('quantity', IntegerType::class,[
            'label' => 'Stock : '
        ])
        ->add('productCondition', ChoiceType::class, [
            'label' => 'Condition : ',
            'choices'  => [
                'Neuf' => "neuf",
                "Occasion" => "occasion",
            ],
        ])
        ->add('excluWeb', CheckboxType::class,[
            'label' => 'Exclusivité Web',
            'required' => false])
        ->add('description', CKEditorType::class,[
            'required' => false,
            'label' => 'Description générale :'
        ])
        ->add('descriptionFast', CKEditorType::class,[
            'required' => false,
            'label' => 'Description rapide :',
            'attr' => [
                'max-length' => "400"
            ]
        ])
        ->add('Valider', SubmitType::class, ['label' => 'Valider'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'translation_domain' => 'forms'
        ]);
    }
}
