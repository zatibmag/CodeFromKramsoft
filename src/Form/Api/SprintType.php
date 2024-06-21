<?php

namespace App\Form\Api;

use App\Entity\Api\Sprint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SprintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('capacity')
            ->add('startAt', DateType::class)
            ->add('endAt', DateType::class)
            ->add('listDoneId')
            ->add('capacityType')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sprint::class,
        ]);
    }
}
