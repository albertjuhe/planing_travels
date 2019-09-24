<?php

namespace App\Infrastructure\TravelBundle\Doctrine\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeoLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lat')
            ->add('lng')
            ->add('lat0')
            ->add('lng0')
            ->add('lat1')
            ->add('lng1')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Domain\Travel\ValueObject\GeoLocation',
        ]);
    }
}
