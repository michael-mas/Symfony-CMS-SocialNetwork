<?php

namespace App\Form;

use App\Entity\Widgets;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WidgetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'categorie-title-input',
                    'placeholder' => 'Nom widget',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'post-content-input',
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
                ],
                'constraints' => [
                    new Length([
                        'max' => 30000
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Widgets::class,
        ]);
    }
}