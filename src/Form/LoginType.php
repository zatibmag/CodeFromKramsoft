<?php

namespace App\Form;

use App\Entity\Api\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Name',
                'attr'  => [
                    'class'        => 'form-control',
                    'autocomplete' => 'username',
                    'required'     => true,
                    'autofocus'    => true,
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr'  => [
                    'class'        => 'form-control',
                    'autocomplete' => 'current-password',
                    'required'     => true,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
