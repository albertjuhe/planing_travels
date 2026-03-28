<?php

namespace App\Infrastructure\UserBundle\Form;

use App\Domain\User\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'First name'])
            ->add('lastName', TextType::class, ['label' => 'Last name'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('username', TextType::class, ['label' => 'Username']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
