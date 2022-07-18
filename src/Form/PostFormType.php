<?php

namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\Length;

class PostFormType extends AbstractType
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
            ->add('content', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'post-content-input',
                    'placeholder' => 'Contenu',
                ],
                'constraints' => [
                    new Length([
                        'max' => 1000
                    ])
                ]
            ])  //hidden inputs
            ->add('images', HiddenType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'post-images-input',
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
            'data_class' => Posts::class,
        ]);
    }
}
