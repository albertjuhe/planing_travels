<?php

namespace App\Infrastructure\TravelBundle\Form;

use App\Domain\Travel\Model\Travel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use App\Infrastructure\TravelBundle\Doctrine\Types\GeoLocationType;

class TravelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('geoLocation', GeoLocationType::class)
            ->add('startAt', DateType::class)
            ->add('endAt', DateType::class)
            ->add('description', TextareaType::class)
            ->add('photo', FileType::class, [
                'label' => 'Cover image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF or WebP)',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Travel::class,
            'csrf_protection' => false,
        ]);
    }
}
