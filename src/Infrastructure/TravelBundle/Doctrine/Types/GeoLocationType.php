<?php

namespace App\Infrastructure\TravelBundle\Doctrine\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeoLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lat', NumberType::class, ['scale' => 6])
            ->add('lng', NumberType::class, ['scale' => 6])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\Domain\Travel\ValueObject\GeoLocation',
        ]);
    }
}
