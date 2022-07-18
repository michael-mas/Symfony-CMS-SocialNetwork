<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('username')
            // ->add('roles')
            // ->add('password')
            ->add('pfp', HiddenType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'user-pfp-input'
                ]
            ])
            ->add('info', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'user-info-input',
                    'placeholder' => 'Info',
                ],
                'constraints' => [
                    new Length([
                        'max' => 200
                    ])
                ]
            ])
            ->add('email', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'user-info-input',
                    'placeholder' => 'Email',
                ],
                'constraints' => [
                    new Length([
                        'max' => 200
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
