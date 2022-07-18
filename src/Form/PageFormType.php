<?php

namespace App\Form;

use App\Entity\Pages;
use App\Entity\Categories;
use App\Entity\Widgets;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File;

class PageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'post-title-input',
                    'placeholder' => 'Titre',
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre titre doit être au moins {{ limit }} caractères',
                        'max' => 50
                    ])
                ]
            ])
            ->add('categories', EntityType::class, [
                'placeholder' => 'categorie',
                'label' => false,
                'row_attr' => ['class' => 'select_cat'],
                'expanded' => false,
                'required' => false,
                'class' => Categories::class,
                'multiple' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'select3'
                ],
                'choices'  => [
                    'template de base' => 0,
                    'template moderne' => 1,
                    'template experimental' => 2,
                ],
            ])
            ->add('widgets', EntityType::class, [
                'placeholder' => 'widget',
                'label' => false,
                'row_attr' => ['class' => 'select_widget'],
                'expanded' => false,
                'required' => false,
                'class' => Widgets::class,
                'multiple' => false,
                'attr' => [
                    'class' => 'select3'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'post-content-input',
                    'placeholder' => 'Contenu 1',
                ],
                'constraints' => [
                    new Length([
                        'max' => 10000
                    ])
                ]
            ])  
            ->add('content2', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'post-content-input',
                    'placeholder' => 'Contenu 2',
                ],
                'constraints' => [
                    new Length([
                        'max' => 10000
                    ])
                ]
            ]) 
            //Entrée caché
            ->add('images', FileType::class, [
                'label' => '+',
                'label_attr' => [
                    'title' => 'Ajouter une image'
                ],
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])
            // ->add('author', HiddenType::class, [
            //     'data' => 1,
            // ])
            // ->add('date', DateTimeType::class, [
            //     'label' => false,
            //     'data' => new DateTime(),
            //     'widget' => 'single_text',
            //     'required' => false,
            //     'attr' => [
            //         'style' => 'display: none;'
            //     ],
            // ])
            // ->add('status', HiddenType::class, [
            //     'data' => 0,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pages::class,
        ]);
    }
}
